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
use database\init\TypingGameTable;
use src\Model\typingGame\ScrappingTypeText;
use src\Model\TypingGame\ScrapingLyrics;

/**
 * Class TypingGameController
 * @package src\Controller
 * コンテンツ画面
 */
class TypingGameController extends BaseController
{
    private $view;
    private $router;
    private $csrf;
    private $flash;
    private $session;
    private $typingGameModel;

    public function __construct(
        \Slim\Views\Twig $view,
        \Slim\Router $router,
        \Slim\Csrf\Guard $csrf,
        \Slim\Flash\Messages $flash,
        \src\SessionHelper $session,
        TypingGameModel $typingGameModel
    ) {
        $this->view = $view;
        $this->router = $router;
        $this->csrf = $csrf;
        $this->flash = $flash;
        $this->session = $session;
        $this->typingGameModel = $typingGameModel;
    }
    
    /**
     * コンテンツ登録
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function register(
        /** @noinspection PhpUnusedParameterInspection */
        Request $request,
        /** @noinspection PhpUnusedParameterInspection */
        Response $response,
        /** @noinspection PhpUnusedParameterInspection */
        array $args
    ) {
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
        if ($result['result'] === 'error') {
            // エラーを返す(URLが正しくない).
            $this->flash->addMessage('error', $result['msg']);
            $uri = $request->getUri()->withPath($this->router->pathFor('index'));
            return $response->withRedirect((string)$uri, 301);
        }
        // 生成できるURLであった.
        elseif ($result['result'] === 'ok') {
            // YoutubeDataAPIからタイトルとサムネイルのURLを取得.
            $youtubeData = $scrappingTypeText->getYoutubeData($settings['settings']['youtube_api']['key']);
            // 字幕取得のためのURL.
            $downloadSubUrl = "https://video.google.com/timedtext?hl=ja&lang=ja&name=&v=" . $videoId;
            // 字幕データ.
            $captionData = $scrappingTypeText->convertToArrayDataFromXML($downloadSubUrl, $settings['settings']['yahoo_api']['key']);
            // $captionData  = [0=>['startTime'=>xxx, 'endTime'=>xxx, 'text'=>xxx, 'Furigana'=>xxx,], 1=>[...], ...]

            // youtube URLであるが字幕データがない
            if (count($captionData) == 0) {
                //歌詞候補選択画面へ
                //引数 videoID, title, thumbnail,
                $uri = $request->getUri()->withPath($this->router->pathFor('typingGameLyricsCandidate', [
                    'isAuth' => $this->session->get('isAuth'),
                    'account' => $this->session->get('account'),
                    'id' => $videoId,
                ]));
                return $response->withRedirect((string)$uri, 301);
            }
            
            // データベース追加.
            $this->typingGameModel->insertData(
                [
                    TypingGameTable::TYPE_TEXT => json_encode($captionData),
                    TypingGameTable::VIDEO_ID => $scrappingTypeText->videoCode,
                    TypingGameTable::TITLE => $youtubeData['title'],
                    TypingGameTable::THUMBNAIL => $youtubeData['thumbnail']
                ]
            );
        } // すでに登録されている.
        elseif ($result['result'] === 'redirect') {
        }
        // リダイレクト.
        $uri = $request->getUri()->withPath($this->router->pathFor('watch', [
            'activeHeader' => 'watch',
            'isAuth' => $this->session->get('isAuth'),
            'account' => $this->session->get('account'),

            'id' => $videoId,// リダイレクトでURLパラメータに使う.
            'videoId' => $videoId
        ]));
        return $response->withRedirect((string)$uri, 301);
    }

    /**
     * 歌詞候補選択画面
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return void
     */
    public function lyricsCandidate(
        Request $request,
        /** @noinspection PhpUnusedParameterInspection */
        Response $response,
        /** @noinspection PhpUnusedParameterInspection */
        array $args
    ) {
        //TODO:画面表示
        $settings = require __DIR__ . '/../settings.php';
        // リクエストパラメータ受け取り.
        $videoId = $args['id'];
        // YoutubeDataAPIからタイトルとサムネイルのURLを取得.
        $scrappingTypeText = new ScrappingTypeText($videoId);
        $youtubeData = $scrappingTypeText->getYoutubeData($settings['settings']['youtube_api']['key']);

        return $this->view->render($response, 'typingGameLyricsCandidate.html.twig', [
            'activeHeader' => 'lyrics-candidate',
            'isAuth' => $this->session->get('isAuth'),
            'account' => $this->session->get('account'),
            'csrf' => parent::generateCsrfKeyValue($request, $this->csrf)['csrf'],

            'videoId' => $videoId,
            'title' => $youtubeData['title'],
            'thumbnail' => $youtubeData['thumbnail']
        ]);
    }

    /**
     * タイトルから歌詞データを取得するAPI
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return void
     */
    public function lyricsSearchApi(
        Request $request,
        /** @noinspection PhpUnusedParameterInspection */
        Response $response,
        /** @noinspection PhpUnusedParameterInspection */
        array $args
    ) {
        //タイトル.
        $title = $request->getQueryParams()['title'];
        $scrapingLyrics = new scrapingLyrics();
        $scrapingLyrics->findLyricsResult($title);
        //スクレイピングで歌詞情報を取得して返す(http://j-lyric.net/)
        $searchResultData = $scrapingLyrics->findLyricsResult($title);
//        $resultData = json_decode($searchResultData);
        return $response->withJson($searchResultData);
    }
    /**
     * 歌詞候補から歌詞を選択し、編集画面へ
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return void
     */
    public function selectLyrics(
        Request $request,
        /** @noinspection PhpUnusedParameterInspection */
        Response $response,
        /** @noinspection PhpUnusedParameterInspection */
        array $args
    ) {
        // TODO: 動画を登録.
        // TODO: 編集画面へリダイレクト.
    }

    /**
     * 一覧
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function list(/** @noinspection PhpUnusedParameterInspection */
        Request $request,
        /** @noinspection PhpUnusedParameterInspection */
        Response $response,
        /** @noinspection PhpUnusedParameterInspection */
        array $args
    ) {
        // アカウント名.
        $accountName = $this->session->get('account');
        // ブックマーク舌動画のみでフィルターするか.
        $isFilterBookmark = $request->getQueryParams()['isFilterBookmark'];
        $isFilterBookmark = $isFilterBookmark === 'true' ? true : false;
        // 動画リストのページ番号.
        $page = $request->getQueryParams()['page'];
        $page = empty($page) ? 1 : $page;
        // 取得する動画の数.
        $movieNum = 30;

        // Output.
        if ($isFilterBookmark) {
            // ブックマーク済みの動画リスト.
            $videoList = $GLOBALS['container']->get('BookmarkModel')->getBookmarkedVideoList($accountName, $page, $movieNum);
            //ブックマーク済み動画リストの次ページ.
            $nextPage = $GLOBALS['container']->get('BookmarkModel')->isExistNextPageMovie($page, $movieNum, $accountName);
        } else {
            // 取得した動画リスト.
            $videoList = $this->typingGameModel->getVideoList($page, $movieNum);
            // 次のページ番号(なければfalse).
            $nextPage = $this->typingGameModel->isExistNextPageMovie($page, $movieNum) ? $page + 1 : false;
        }
        // 前のページ番号(なければfalse).
        $prevPage = $page > 1 ? $page - 1 : false;
        return $this->view->render($response, 'typingGameList.html.twig', [
            'activeHeader' => 'list',
            'isAuth' => $this->session->get('isAuth'),
            'account' => $this->session->get('account'),

            'videoList' => $videoList,
            'page' => $page,
            'prevPage' => $prevPage,
            'nextPage' => $nextPage,
            'isFilterBookmark' => $isFilterBookmark
        ]);
    }

    /**
     * 個別のコンテンツ
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function content(/** @noinspection PhpUnusedParameterInspection */
        Request $request,
        /** @noinspection PhpUnusedParameterInspection */
        Response $response,
        /** @noinspection PhpUnusedParameterInspection */
        array $args
    ) {
        // リクエストパラメータ受け取り.
        $videoId = $args['id'];
        $title = $this->typingGameModel->searchVideoInfo($videoId)[TypingGameTable::TITLE];
        // ブックマークしているか確認.
        if ($this->session->get('isAuth')) {
            $isBookmark = $GLOBALS['container']->get('BookmarkModel')->isBookmark($this->session->get('account'), $videoId);
        } else {
            $isBookmark = false;
        }
        return $this->view->render($response, 'typingGameContent.html.twig', [
            'activeHeader' => 'watch',
            'isAuth' => $this->session->get('isAuth'),
            'account' => $this->session->get('account'),
            'csrf' => parent::generateCsrfKeyValue($request, $this->csrf)['csrf'],

            'videoId' => $videoId,
            'isBookmark' => $isBookmark,
            'title' => $title
        ]);
    }

    /**
     * 個別のコンテンツの編集画面
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function editView(/** @noinspection PhpUnusedParameterInspection */
        Request $request,
        /** @noinspection PhpUnusedParameterInspection */
        Response $response,
        /** @noinspection PhpUnusedParameterInspection */
        array $args
    ) {
        // リクエストパラメータ受け取り.
        $videoId = $args['id'];
        $title = $this->typingGameModel->searchVideoInfo($videoId)[TypingGameTable::TITLE];

        return $this->view->render($response, 'typingGameEdit.html.twig', [
            'activeHeader' => 'edit',
            'isAuth' => $this->session->get('isAuth'),
            'account' => $this->session->get('account'),
            'csrf' => parent::generateCsrfKeyValue($request, $this->csrf)['csrf'],

            'videoId' => $videoId,
            'title' => $title
        ]);
    }

    /**
     * 編集結果の保存
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function saveContent(/** @noinspection PhpUnusedParameterInspection */
        Request $request,
        /** @noinspection PhpUnusedParameterInspection */
        Response $response,
        /** @noinspection PhpUnusedParameterInspection */
        array $args
    ) {
        $videoId = $request->getParsedBody()['videoId'];
        $typeInfo = $request->getParsedBody()['typeInfo'];
        // jsonにしてDB保存.
        $this->typingGameModel->updateTypeInfo($videoId, $typeInfo);

        // リダイレクト.
        $uri = $request->getUri()->withPath($this->router->pathFor('watch', [
            'activeHeader' => 'watch',
            'isAuth' => $this->session->get('isAuth'),
            'account' => $this->session->get('account'),

            'id' => $videoId,// リダイレクトでURLパラメータに使う.
            'videoId' => $videoId
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
    public function getTypeTextApi(/** @noinspection PhpUnusedParameterInspection */
        Request $request,
        /** @noinspection PhpUnusedParameterInspection */
        Response $response,
        /** @noinspection PhpUnusedParameterInspection */
        array $args
    ) {
        $videoId = $request->getQueryParams()['videoId'];
        $resultData = json_decode($this->typingGameModel->searchVideoInfo($videoId)[TypingGameTable::TYPE_TEXT]);
        return $response->withJson($resultData);
    }
}
