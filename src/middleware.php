<?php
// Application middleware

// ↑ 内側

// CSRF対策.
//$app->add($container->get('csrf'));

// セッション.
//$app->add(new \Slim\Middleware\Session([]));


// 認証.
//$app->add($container->get('accountAuth'));


// CORS（クロスサイトでAjax）
$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', 'http://localhost:8083')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

// ↓ 外側
