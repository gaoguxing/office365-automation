<?php

return [
    'settings' => [
        'displayErrorDetails' => getenv('APP_DEBUG'), // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'office365-api',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app-'. date('Y-m-d') .'.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        // Microsoft Graph
        'microsoft-graph' => [
            'login_url' => 'https://login.microsoftonline.com/' . getenv('DIRECTORY_ID') . '/oauth2/token?api-version=1.0',
            'timeout' => 15,
            'client_id' => getenv('CLIENT_ID'),
            'client_secret' => getenv('CLIENT_SECRET'),
            'domain' => getenv('DOMAIN'),
            'usage_location' => getenv('USAGE_LOCATION')
        ]
    ],
];
