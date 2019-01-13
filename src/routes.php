<?php


// Routes

// setName()で名前を割当ることで、PathFor(name,[index, value])でURLを生成できる

$app->group('', function () use ($app) {
    // トップページ.
    $app->get('/', 'MainApp' . ':index')->setName('index');

    // ログイン・ログアウト.
    $app->get('/signIn', 'AccountController' . ':signIn')->setName('signIn');
    $app->get('/signUp', 'AccountController' . ':signUp')->setName('signUp');
    $app->get('/logout', 'AccountController' . ':logout')->setName('logout');
    //$app->get('/signUpTest','AccountController' . ':test')->setName('accountTest');

    // コンテンツ1に新規登録.
    $app->get('/content1/new', 'Content1' . ':new')->setName('content1New');
    // コンテンツ1にコンテンツ登録.
    $app->post('/content1/register', 'Content1' . ':register')->setName('content1Register');
    $app->get('/content1/list', 'Content1' . ':list')->setName('contentsList');
    // タイピング画面
    $app->get('/content1/watch/{id}', 'Content1' . ':content')->setName('watch');
    // タイピング編集画面.
    $app->get('/content1/edit/{id}', 'Content1' . ':editView')->setName('edit');
    // 編集結果の保存.
    $app->post('/content1/saveContent', 'Content1' . ':saveContent')->setName('saveContent');
    // タイピングテキストを取得(web API).
    $app->get('/getTypeText', 'Content1' . ':getTypeTextApi');

    // コンテンツ1.
    $app->get('/content1', 'Content1' . ':index')->setName('content1');
    // コンテンツ2.
    $app->get('/content2', 'Content2' . ':index')->setName('content2');

    // 認証.
    $app->post('/auth', 'AccountController' . ':auth')->setName('auth');

    // 登録.
    $app->post('/registerAccount', 'AccountController' . ':registerAccount')
        ->setName('registerAccount')
        ->add(new \DavidePastore\Slim\Validation\Validation($GLOBALS['validators']));

})->add($container->get('csrf'));

// GitHubからWebHook.
$app->post('/gitHubWebHook','DeployController'.':gitHubWebHook');
