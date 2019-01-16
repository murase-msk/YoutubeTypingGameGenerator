<?php
/**
 * Created by PhpStorm.
 * User: masaki
 * Date: 2019/01/15
 * Time: 19:50
 */

namespace src\Controller;

use Slim\Http\Response;
use Slim\Http\Request;
use src\Model\BookmarkModel;


class BookmarkController extends BaseController
{

    private $view;
    private $router;
    private $csrf;
    private $flash;
    private $session;
    private $bookmarkModel;

    function __construct(\Slim\Views\Twig $view,
                         \Slim\Router $router,
                         \Slim\Csrf\Guard $csrf,
                         \Slim\Flash\Messages $flash,
                         \src\SessionHelper $session,
                         BookmarkModel $bookmarkModel)
    {
        $this->view = $view;
        $this->router = $router;
        $this->csrf = $csrf;
        $this->flash = $flash;
        $this->session = $session;
        $this->bookmarkModel = $bookmarkModel;
    }

    /**
     * ブックマークしているか判定
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    function isBookmark(
        /** @noinspection PhpUnusedParameterInspection */
        Request $request,
        /** @noinspection PhpUnusedParameterInspection */
        Response $response,
        /** @noinspection PhpUnusedParameterInspection */
        array $args)
    {
        if($this->session->get('isAuth')){
            $accountName = $this->session->get('account');
            $videoId = $request->getParsedBody()['videoId'];
            $isBookmark = $this->bookmarkModel->isBookmark($accountName, $videoId);
            return $response->withJson(["noError"=>true, "isBookmark"=>$isBookmark]);
        }
        return $response->withJson(["noError"=>false]);
    }

    /**
     * ブックマークの状態を変更する
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    function changeBookmark(
        /** @noinspection PhpUnusedParameterInspection */
        Request $request,
        /** @noinspection PhpUnusedParameterInspection */
        Response $response,
        /** @noinspection PhpUnusedParameterInspection */
        array $args)
    {
        if($this->session->get('isAuth')){
            $accountName = $this->session->get('account');
            $isBookmark = $account = $request->getParsedBody()['isBookmark'];
            $videoId = $account = $request->getParsedBody()['videoId'];
            if($isBookmark){
                // ブックマーク削除.
                $this->bookmarkModel->deleteBookmark($accountName, $videoId);
            }else{
                // ブックマーク登録.
                $this->bookmarkModel->registerBookmark($accountName, $videoId);
            }
            return $response->withJson(["noError"=>true, "isBookmark"=>!$isBookmark]);
        }
        return $response->withJson(["noError"=>false]);
    }
}