<?php

namespace src\Controller;

use src\Model\SampleModel;
use Slim\Http\Response;
use Slim\Http\Request;

class MainApp extends BaseController
{
    private $view;
    private $session;
    private $csrf;
    private $flash;

    public function __construct(
        \Slim\Views\Twig $view,
        \Slim\Csrf\Guard $csrf,
        \Slim\Flash\Messages $flash,
        \src\SessionHelper $session
    ) {
        $this->view = $view;
        $this->csrf = $csrf;
        $this->flash = $flash;
        $this->session = $session;
    }

    public function index(
        /** @noinspection PhpUnusedParameterInspection */
        Request $request,
        /** @noinspection PhpUnusedParameterInspection */
        Response $response,
        /** @noinspection PhpUnusedParameterInspection */
        array $args
    ) {
        // フラッシュメッセージ取得.
        $flash = $this->flash->getMessages();
        //$result = $this->sampleModel->getData();
        return $this->view->render($response, 'mainPage.html.twig', [
            'activeHeader' => 'index',
            'isAuth' => $this->session->get('isAuth'),
            'account' => $this->session->get('account'),
            'flash' => $flash,
            'csrf' => parent::generateCsrfKeyValue($request, $this->csrf)['csrf'],
        ]);
    }
}
