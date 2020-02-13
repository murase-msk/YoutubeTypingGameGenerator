<?php


// Routes

// setName()で名前を割当ることで、PathFor(name,[index, value])でURLを生成できる

$app->group('', function () use ($app) {
    // トップページ.
    $app->get('/', 'MainApp' . ':index')->setName('index');

    // ログイン.
    $app->get('/signIn', 'AccountController' . ':signIn')->setName('signIn');
    // 新規登録.
    $app->get('/signUp', 'AccountController' . ':signUp')->setName('signUp');
    // ログアウト.
    $app->get('/logout', 'AccountController' . ':logout')->setName('logout');
    // 認証.
    $app->post('/auth', 'AccountController' . ':auth')->setName('auth');
    // 登録処理.
    $app->post('/registerAccount', 'AccountController' . ':registerAccount')
        ->setName('registerAccount')
        ->add(new \DavidePastore\Slim\Validation\Validation($GLOBALS['validators']));

    // コンテンツ1に新規登録.
    $app->get('/typingGame/new', 'TypingGameController' . ':new')->setName('typingGameNew');
    // コンテンツ1にコンテンツ登録.
    $app->post('/typingGame/register', 'TypingGameController' . ':register')->setName('typingGameRegister');
    // 歌詞候補選択画面
    $app->get('/typingGame/lyricsCandidate/{id}', 'TypingGameController' . ':lyricsCandidate')->setName('typingGameLyricsCandidate');
    // 歌詞選択
    $app->get('/typingGame/selectLyrics', 'TypingGameController' . ':selectLyrics')->setName('typingGameSelectLyrics');
    // 一覧
    $app->get('/typingGame/list', 'TypingGameController' . ':list')->setName('contentsList');
    // タイピング画面
    $app->get('/typingGame/watch/{id}', 'TypingGameController' . ':content')->setName('watch');
    // タイピング編集画面.
    $app->get('/typingGame/edit/{id}', 'TypingGameController' . ':editView')->setName('edit');
    // 編集結果の保存.
    $app->post('/typingGame/saveContent', 'TypingGameController' . ':saveContent')->setName('saveContent');
    // タイピングテキストを取得(web API).
    $app->get('/typingGame/getTypeText', 'TypingGameController' . ':getTypeTextApi');
    // コンテンツ1.
    //$app->get('/typingGame', 'TypingGameController' . ':index')->setName('typingGame');

    // ブックマークしているかどうか.
    $app->post('/bookmark/isBookmark', 'BookmarkController' . ':isBookmark')->setName('isBookmark');
    // ブックマーク状態を変える.
    $app->post('/bookmark/changeBookmark', 'BookmarkController' . ':changeBookmark')->setName('changeBookmark');

    // コンテンツ2.
    //$app->get('/content2', 'Content2' . ':index')->setName('content2');


})->add($container->get('csrf'));

// GitHubからWebHook.
$app->post('/gitHubWebHook', 'DeployController' . ':gitHubWebHook');
