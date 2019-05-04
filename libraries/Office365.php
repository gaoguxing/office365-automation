<?php

namespace Libraries;

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model\User;
use GuzzleHttp\Client as HttpClient;
use Microsoft\Graph\Model\SubscribedSku;
use Microsoft\Graph\Model\AssignedLicense;

class Office365
{
    protected $app;

    protected static $instance;

    protected $config;

    protected $graph;

    protected function __construct()
    {
        $this->app = app()->getContainer();
        $this->config = $this->app->get('settings')['microsoft-graph'];

        $this->graph = new Graph;
        $this->graph->setAccessToken($this->getAccessToken());
    }

    protected function __clone() {}

    public static function instance()
    {
        return self::$instance instanceof self ? self::$instance : self::$instance = new self;
    }

    protected function getAccessToken()
    {
        $guzzle = new HttpClient([
            'base_uri' => $this->config['login_url'],
            'timeout' => $this->config['timeout']
        ]);

        $response = $guzzle->post('', [
            'form_params' => [
                'client_id' => $this->config['client_id'],
                'client_secret' => $this->config['client_secret'],
                'resource' => 'https://graph.microsoft.com/',
                'grant_type' => 'client_credentials',
            ],
        ])
        ->getBody()
        ->getContents();

        $accessToken = json_decode($response, true);

        return $accessToken['access_token'];
    }

    protected function getSkuId()
    {
        $skus = $this->graph->createRequest('GET', '/subscribedSkus')
            ->setReturnType(SubscribedSku::class)
            ->execute();

        if (! isset($skus[0])) {
            throw new UnexpectedValueException('No skus');
        }

        return $skus[0]->getSkuId();
    }

    public function createUser($payload)
    {
        $newUser = new User([
            'accountEnabled' => true,
            'displayName' => $payload['nickname'],
            'mailNickname' => $payload['mail_nickname'],
            'userPrincipalName' => $payload['mail_nickname'] . '@' . $this->config['domain'],
            'usageLocation' => $this->config['usage_location'],
            'passwordProfile' => [
                'forceChangePasswordNextSignIn' => false,
                'password' => $payload['password']
            ]
        ]);

        $user = $this->graph->createRequest('POST', '/users')
            ->attachBody($newUser)
            ->setReturnType(User::class)
            ->execute();

        $this->assignLicense($user->getId());
    }

    protected function assignLicense($userId)
    {
        $newLicense = new AssignedLicense([
            'addLicenses' => [[
                'disabledPlans' => [],
                'skuId' => $this->getSkuId()
            ]],
            'removeLicenses' => []
        ]);

        $this->graph->createRequest('POST', '/users/' . $userId . '/assignLicense')
            ->attachBody($newLicense)
            ->setReturnType(AssignedLicense::class)
            ->execute();
    }

    public function deleteUser()
    {
        //
    }
}
