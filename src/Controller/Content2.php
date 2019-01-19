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

/**
 * Class Content1
 * @package src\Controller
 * コンテンツ画面
 */
class Content2
{

    private $view;
    private $session;

    function __construct(\Slim\Views\Twig $view, \src\SessionHelper $session)
    {
        $this->view = $view;
        $this->session = $session;
    }

    function index(
        /** @noinspection PhpUnusedParameterInspection */
        Request $request,
        /** @noinspection PhpUnusedParameterInspection */
        Response $response,
        /** @noinspection PhpUnusedParameterInspection */
        array $args)
    {
        return $this->view->render($response, 'content2.html.twig', [
            'activeHeader' => 'content2',
            'isAuth' => $this->session->get('isAuth'),
            'account' => $this->session->get('account'),
        ]);
    }

}