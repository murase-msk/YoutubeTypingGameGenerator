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
use src\Model\typingGame\ValidationVideo;
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

        $validationVideo = new ValidationVideo($youtubeUrl);
        $videoId = $validationVideo->videoId;
        $scrappingTypeText = new ScrappingTypeText($videoId);
        // TODO: クライアントの日本語と英語選択を反映する
        $result = $validationVideo->validateUrl(
            $this->typingGameModel,
            'Japanese',
            $scrappingTypeText
        );
        // 何らかの異常があり、生成できない.
        if($result['result'] === 'error'){
            // エラーを返す(URLが正しくない).
            $this->flash->addMessage('error', $result['msg']);
            $uri = $request->getUri()->withPath($this->router->pathFor('index'));
            return $response->withRedirect((string)$uri, 301);
        }
        // 生成できるURLであった.
        else if($result['result'] === 'ok') {
            $downloadSubUrl = $scrappingTypeText->getSrtUrl($validationVideo->langListIndex);
            $captionData = $scrappingTypeText->convertToArrayDataFromSrtSubUrl($downloadSubUrl, $settings['settings']['yahoo_api']['key']);
            // $captionData  = [0=>['startTime'=>xxx, 'endTime'=>xxx, 'text'=>xxx, 'Furigana'=>xxx,], 1=>[...], ...]
            // YoutubeDataAPIからタイトルとサムネイルのURLを取得.
            $youtubeData = $scrappingTypeText->getYoutubeData($settings['settings']['youtube_api']['key']);
            // データベース追加.
            $this->typingGameModel->insertData(
                [
                    'type_text' => json_encode($captionData),
                    'video_code' => $scrappingTypeText->videoCode,
                    'title' => $youtubeData['title'],
                    'thumbnail' => $youtubeData['thumbnail']
                ]);
        }
        // すでに登録されている.
        else if($result['result'] === 'redirect'){
        }
        // リダイレクト.
        $uri = $request->getUri()->withPath($this->router->pathFor('watch', [
            'activeHeader' => 'watch',
            'isAuth' => $this->session->get('isAuth'),
            'account' => $this->session->get('account'),

            'id'=>$videoId,// リダイレクトでURLパラメータに使う.
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
        // ログインしているか.
        $isAuth = $this->session->get('isAuth');
        // アカウント名.
        $accountName = $this->session->get('account');
        // ブックマーク舌動画のみでフィルターするか.
        $isFilterBookmark = $request->getQueryParams()['isFilterBookmark'];
        $isFilterBookmark =$isFilterBookmark === 'true' ? true : false;
        // 動画リストのページ番号.
        $page = $request->getQueryParams()['page'];
        $page = empty($page) ? 1 : $page;
        // 取得する動画の数.
        $movieNum=30;

        // Output.
        if($isFilterBookmark) {
            // ブックマーク済みの動画リスト.
            $videoList = $GLOBALS['container']->get('BookmarkModel')->getBookmarkedVideoList($accountName, $page, $movieNum);
            //ブックマーク済み動画リストの次ページ.
            $nextPage = $GLOBALS['container']->get('BookmarkModel')->isExistNextPageMovie($page, $movieNum, $accountName);
        }else{
            // 取得した動画リスト.
            $videoList = $this->typingGameModel->getVideoList($page, $movieNum);
            // 次のページ番号(なければfalse).
            $nextPage = $this->typingGameModel->isExistNextPageMovie($page, $movieNum) ? $page + 1: false;
        }
        // 前のページ番号(なければfalse).
        $prevPage = $page > 1 ? $page - 1 : false;
        return $this->view->render($response, 'content1List.html.twig', [
            'activeHeader' => 'content1',
            'isAuth' => $this->session->get('isAuth'),
            'account' => $this->session->get('account'),

            'videoList'=>$videoList,
            'page'=>$page,
            'prevPage'=>$prevPage,
            'nextPage'=>$nextPage,
            'isFilterBookmark'=>$isFilterBookmark
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
        // ブックマークしているか確認.
        if($this->session->get('isAuth')) {
            $isBookmark = $GLOBALS['container']->get('BookmarkModel')->isBookmark($this->session->get('account'), $videoId);
        }else{
            $isBookmark = false;
        }
        return $this->view->render($response, 'content1Content.html.twig', [
            'activeHeader' => 'watch',
            'isAuth' => $this->session->get('isAuth'),
            'account' => $this->session->get('account'),
            'csrf' => parent::generateCsrfKeyValue($request, $this->csrf)['csrf'],

            'videoId'=>$videoId,
            'isBookmark'=>$isBookmark
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

            'id'=>$videoId,// リダイレクトでURLパラメータに使う.
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