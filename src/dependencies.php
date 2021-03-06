<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
// $container['renderer'] = function ($c) {
//     $settings = $c->get('settings')['renderer'];
//     return new Slim\Views\PhpRenderer($settings['template_path']);
// };

// セッション
$container['session'] = function ($c) {
    //return new \SlimSession\Helper;
    return new \src\SessionHelper;
};

// フラッシュメッセージ.
$container['flash'] = function ($c) {
    return new \Slim\Flash\Messages();
};

// アカウント認証.
$container['accountAuth'] = function ($c) {
    // TODO: 未完成
    $accountAuth = new \src\Middleware\Auth\AccountAuth();
    return $accountAuth;
};

// CSRF
$container['csrf'] = function ($c) {
    $guard = new \Slim\Csrf\Guard;
    // トークン永続化.
    $guard->setPersistentTokenMode(true);
    // CSRFチェック失敗時.
//    $guard->setFailureCallable(function ($request, $response, $next) {
//        $request = $request->withAttribute("csrf_status", false);
//        return $next($request, $response);
//    });
    return $guard;
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// twig
$container['view'] = function ($container) {
    $settings = $container->get('settings');

    $cachePath = $settings['env'] === 'dev' ? false : $settings['cache']['cache_path'];
    $view = new \Slim\Views\Twig($settings['renderer']['template_path'], [
        'cache' => $cachePath
    ]);

    // Instantiate and add Slim specific extension
    $a = $container->get('request')->getUri()->getBasePath();
    $basePath = rtrim(str_ireplace('index.php', '', $a), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container->get('router'), $basePath));

    return $view;
};
// DB.
$container['db'] = function ($container) {
    $db = $container['settings']['db'];
    $dsn = 'pgsql:host=' . $db['host'] . '; port=' . $db['port'] . ';dbname=' . $db['dbname'];
    $pdo = new PDO($dsn, $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

//
// Model
//

$container['accountModel'] = function ($container) {
    $accountModel = new \src\Model\AccountModel($container['db']);
    return $accountModel;
};
$container['TypingGameModel'] = function ($container) {
    $typingGameModel = new \src\Model\TypingGameModel($container['db']);
    return $typingGameModel;
};
$container['BookmarkModel'] = function ($container) {
    $typingGameModel = new \src\Model\BookmarkModel($container['db']);
    return $typingGameModel;
};

//
// Controller
//
$container['MainApp'] = function ($container) {
    $view = $container->get('view');
    $csrf = $container->get('csrf');
    $flash = $container->get('flash');
    $session = $container->get('session');
    return new \src\Controller\MainApp($view, $csrf, $flash, $session);
};

$container['TypingGameController'] = function ($container) {
    $view = $container->get('view');
    $router = $container->get('router');
    $csrf = $container->get('csrf');
    $flash = $container->get('flash');
    $session = $container->get('session');
    $typingGameModel = $container->get('TypingGameModel');
    return new \src\Controller\TypingGameController($view, $router, $csrf, $flash, $session, $typingGameModel);
};

$container['BookmarkController'] = function ($container) {
    $view = $container->get('view');
    $router = $container->get('router');
    $csrf = $container->get('csrf');
    $flash = $container->get('flash');
    $session = $container->get('session');
    $bookmarkModel = $container->get('BookmarkModel');
    return new \src\Controller\BookmarkController($view, $router, $csrf, $flash, $session, $bookmarkModel);
};

//$container['Content2'] = function ($container) {
//    $view = $container->get('view');
//    $session = $container->get('session');
//    return new \src\Controller\Content2($view, $session);
//};


$container['AccountController'] = function ($container) {
    $view = $container->get('view');
    $accountModel = $container->get('accountModel');
    $router = $container->get('router');
    $csrf = $container->get('csrf');
    $flash = $container->get('flash');
    $session = $container->get('session');
    return new \src\Controller\AccountController($view, $accountModel, $router, $csrf, $flash, $session);
};

$container['DeployController'] = function ($container) {
    return new \src\Controller\DeployController();
};
