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
            'user' => 'root',
            'pass' => 'root',
            'dbname'=> 'testdb'
        ],
        'db_psql' => [
            'host' => 'localhost',
            'user' => 'postgres',
            'pass' => 'postgres',
            'dbname'=> 'slim_app'
       ],
        'db_psql_typing_game'=>[
            'host' => 'localhost',
            'user' => 'postgres',
            'pass' => 'postgres',
            'dbname'=> 'typing_game_auto_generator'
        ],
        'root_account'=>[
            'name'=>getenv('ROOT_NAME'),
            'pass'=>getenv('ROOT_PASS')
        ],
        'youtube_api'=>[
            'key'=>getenv('YOUTUBE_API_KEY')
        ]
    ],
];