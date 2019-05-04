<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Libraries\Office365;
use Respect\Validation\Validator;

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
                $succeed = Office365::instance()->createUser($request->getParsedBody());

                if ($succeed === true) {
                    return $response->withStatus(201)->withJson(['status' => true]);
                } else {
                    return $response->withStatus(400)->withJson(['status' => false, 'message' => $succeed]);
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
