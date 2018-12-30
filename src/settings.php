<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        'env' => 'dev', // dev 開発用, production 本番.

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        'cache' => [
            'cache_path' => __DIR__ .'/../cache/',
        ],

        // Monolog settings
//        'logger' => [
//            'name' => 'slim-app',
//            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
//            'level' => \Monolog\Logger::DEBUG,
//        ],
        'db' => [
            'host' => 'localhost',
            'user' => getenv('DB_user'),
            'pass' => getenv('DB_pass'),
            'dbname'=> 'youtube_typing_game_generator',
            'port' => '5432'
        ],
        'root_account'=>[
            'name'=>getenv('ROOT_NAME'),
            'pass'=>getenv('ROOT_PASS')
        ],
        'youtube_api'=>[
            'key'=>getenv('youtubeApiKey')
        ],
        'yahoo_api' =>[
            'key'=>getenv('yahooApiKey')
        ]
    ],
];
