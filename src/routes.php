<?php

use Slim\App;
use Exception;
use Slim\Http\Request;
use Slim\Http\Response;
use Libraries\Office365;
use Respect\Validation\Validator;
use GuzzleHttp\Exception\ServerException;

return function (App $app) {
    $container = $app->getContainer();

    $app->group('/api/v1', function () use ($app, $container) {
        $app->post('/users', function (Request $request, Response $response, array $args) use ($container) {
            $validator = $container->validator->validate($request, [
                'nickname' => Validator::length(6, 32),
                'mail_nickname' => Validator::length(6, 32)->alnum('-_.')->noWhitespace(),
                'password' => Validator::length(6, 32)->noWhitespace()
            ]);

            if ($validator->isValid()) {
                try {
                    Office365::instance()->createUser($request->getParsedBody());

                    return $response->withStatus(201)->withJson(['status' => true]);
                } catch (ServerException | Exception $e){
                    return $response->withStatus(400)->withJson([
                        'status' => false,
                        'errors' => json_decode($e->getResponse()->getBody()->getContents())
                    ]);
                }
            } else {
                return $response->withStatus(421)->withJson([
                    'status' => false,
                    'errors' => $validator->getErrors()
                ]);
            }
        });
    });
};
