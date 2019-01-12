<?php
/**
 * Created by PhpStorm.
 * User: masaki
 * Date: 2018/08/13
 * Time: 13:25
 */

namespace src\Controller;
use Slim\Http\Response;
use Slim\Http\Request;
use src\Model\TypingGameModel;
use src\Model\typingGame\ConvertTypeText;
use src\Model\typingGame\ScrappingTypeText;


/**
 * Class Content1
 * @package src\Controller
 * コンテンツ画面
 */
class Content1 extends BaseController
{

    private $view;
    private $router;
    private $csrf;
    private $flash;
    private $session;
    private $typingGameModel;

    function __construct(
        \Slim\Views\Twig $view,
        \Slim\Router $router,
        \Slim\Csrf\Guard $csrf,
        \Slim\Flash\Messages $flash,
        \src\SessionHelper $session,
        TypingGameModel $typingGameModel)
    {
        $this->view = $view;
        $this->router = $router;
        $this->csrf = $csrf;
        $this->flash = $flash;
        $this->session = $session;
        $this->typingGameModel = $typingGameModel;
    }

    function index(
        /** @noinspection PhpUnusedParameterInspection */
        Request $request,
        /** @noinspection PhpUnusedParameterInspection */
        Response $response,
        /** @noinspection PhpUnusedParameterInspection */
        array $args)
    {

        return $this->view->render($response, 'content1.html.twig', [
            'activeHeader' => 'content1',
            'isAuth' => $this->session->get('isAuth'),
            'account' => $this->session->get('account'),
        ]);
    }

    /**
     * 新規作成画面
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    function new(
        /** @noinspection PhpUnusedParameterInspection */
        Request $request,
        /** @noinspection PhpUnusedParameterInspection */
        Response $response,
        /** @noinspection PhpUnusedParameterInspection */
        array $args)
    {
        return $this->view->render($response, 'content1New.html.twig', [
            'activeHeader' => 'content1',
            'isAuth' => $this->session->get('isAuth'),
            'account' => $this->session->get('account'),
        ]);
    }

    /**
     * コンテンツ登録
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    function register(
        /** @noinspection PhpUnusedParameterInspection */
        Request $request,
        /** @noinspection PhpUnusedParameterInspection */
        Response $response,
        /** @noinspection PhpUnusedParameterInspection */
        array $args)
    {
        $settings = require __DIR__ . '/../settings.php';
        $youtubeUrl = $request->getParsedBody()['youtube_url'];
        // youtubeの動画URLであるかチェック
        $isMatch = preg_match('/^(http|https):\/\/(www\.youtube\.com\/watch\?v=)([A-Z0-9_-]+)(&.*)?/i', $youtubeUrl, $matchResult);
        if($isMatch !== 1){
            // エラーを返す(URLが正しくない).
            $this->flash->addMessage('error', 'URLが正しくありません');
            $uri = $request->getUri()->withPath($this->router->pathFor('index'));
            return $response->withRedirect((string)$uri, 301);
        }
        // TODO:すでに登録されている動画であるか.
        if(true){

        }
        $videoId = $matchResult[3];
        // videoIdから字幕情報タイピング情報取得.
        $scrappingTypeText = new ScrappingTypeText($youtubeUrl);
        $languageList = $scrappingTypeText->getScriptLanguageList();
        $langListIndex = array_search('Japanese', $languageList);
        if($langListIndex === false){
            // 対応する字幕が見つからなかった。.
            $this->flash->addMessage('error', '対応する字幕データがありません');
            $uri = $request->getUri()->withPath($this->router->pathFor('index'));
            return $response->withRedirect((string)$uri, 301);
        }
        $downloadSubUrl = $scrappingTypeText->getSrtUrl($langListIndex);
        $captionData = $scrappingTypeText->convertToArrayDataFromSrtSubUrl($downloadSubUrl, $settings['settings']['yahoo_api']['key']);
        // $captionData  = [0=>['startTime'=>xxx, 'endTime'=>xxx, 'text'=>xxx, 'Furigana'=>xxx,], 1=>[...], ...]
        // YoutubeDataAPIからタイトルとサムネイルのURLを取得.
        $youtubeData = $scrappingTypeText->getYoutubeData($settings['settings']['youtube_api']['key']);
        // データベース追加.
        $this->typingGameModel->insertData(
            [
                'type_text'=>json_encode($captionData),
                'video_code'=>$scrappingTypeText->videoCode,
                'title' =>$youtubeData['title'],
                'thumbnail'=>$youtubeData['thumbnail']
            ]);

        // リダイレクト.
        $uri = $request->getUri()->withPath($this->router->pathFor('watch', [
            'activeHeader' => 'watch',
            'isAuth' => $this->session->get('isAuth'),
            'account' => $this->session->get('account'),

            'id'=>$videoId,
            'videoId'=>$videoId
        ]));
        return $response->withRedirect((string)$uri, 301);

    }

    /**
     * 一覧
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    function list(/** @noinspection PhpUnusedParameterInspection */
        Request $request,
        /** @noinspection PhpUnusedParameterInspection */
        Response $response,
        /** @noinspection PhpUnusedParameterInspection */
        array $args)
    {
        $page = $request->getQueryParams()['page'];
        $page = empty($page) ? 1 : $page;
        $movieNum=30;   // 取得する動画の数.
        // TODO: DBからリスト取得.
        $videoList = $this->typingGameModel->getVideoList($page, $movieNum);

        $prevPage = $page > 1 ? $page - 1 : false;
        $nextPage = $this->typingGameModel->isExistNextPageMovie($page, $movieNum) ? $page + 1: false;

        // TODO: リンクを表示
        return $this->view->render($response, 'content1List.html.twig', [
            'activeHeader' => 'content1',
            'isAuth' => $this->session->get('isAuth'),
            'account' => $this->session->get('account'),

            'videoList'=>$videoList,
            'page'=>$page,
            'prevPage'=>$prevPage,
            'nextPage'=>$nextPage
        ]);
    }

    /**
     * 個別のコンテンツ
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    function content(/** @noinspection PhpUnusedParameterInspection */
        Request $request,
        /** @noinspection PhpUnusedParameterInspection */
        Response $response,
        /** @noinspection PhpUnusedParameterInspection */
        array $args)
    {
        // リクエストパラメータ受け取り.
        $videoId = $args['id'];

        return $this->view->render($response, 'content1Content.html.twig', [
            'activeHeader' => 'watch',
            'isAuth' => $this->session->get('isAuth'),
            'account' => $this->session->get('account'),

            'videoId'=>$videoId
        ]);
    }

    /**
     * 個別のコンテンツの編集画面
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    function editView(/** @noinspection PhpUnusedParameterInspection */
        Request $request,
        /** @noinspection PhpUnusedParameterInspection */
        Response $response,
        /** @noinspection PhpUnusedParameterInspection */
        array $args)
    {
        // リクエストパラメータ受け取り.
        $videoId = $args['id'];

        return $this->view->render($response, 'content1Edit.html.twig', [
            'activeHeader' => 'edit',
            'isAuth' => $this->session->get('isAuth'),
            'account' => $this->session->get('account'),
            'csrf' => parent::generateCsrfKeyValue($request, $this->csrf)['csrf'],

            'videoId'=>$videoId
        ]);
    }

    /**
     * 編集結果の保存
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    function saveContent(/** @noinspection PhpUnusedParameterInspection */
        Request $request,
        /** @noinspection PhpUnusedParameterInspection */
        Response $response,
        /** @noinspection PhpUnusedParameterInspection */
        array $args)
    {
        $videoId = $request->getParsedBody()['videoId'];
        $typeInfo= $request->getParsedBody()['typeInfo'];
        // jsonにしてDB保存.
        $this->typingGameModel->updateTypeInfo($videoId, $typeInfo);

        // リダイレクト.
        $uri = $request->getUri()->withPath($this->router->pathFor('watch', [
            'activeHeader' => 'watch',
            'isAuth' => $this->session->get('isAuth'),
            'account' => $this->session->get('account'),

            'id'=>$videoId,
            'videoId'=>$videoId
        ]));
        return $response->withRedirect((string)$uri, 301);

    }

    /**
     * タイピングテキストを取得するWebAPI
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    function getTypeTextApi(/** @noinspection PhpUnusedParameterInspection */
        Request $request,
        /** @noinspection PhpUnusedParameterInspection */
        Response $response,
        /** @noinspection PhpUnusedParameterInspection */
        array $args)
    {
        $videoId = $request->getQueryParams()['videoId'];
        $resultData = $this->typingGameModel->searchTypeText($videoId);
        return $response->withJson($resultData);
    }
}