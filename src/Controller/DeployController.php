<?php
/**
 * Created by PhpStorm.
 * User: masaki
 * Date: 2018/10/08
 * Time: 19:12
 */

namespace src\Controller;

use Slim\Http\Response;
use Slim\Http\Request;

/**
 * Class DeployController
 * @package src\Controller
 */
class DeployController
{

    function __construct()
    {
        return;
    }

    /**
     * GitHookをトリガーとしてGithubからプルする
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function gitHubWebHook(
        /** @noinspection PhpUnusedParameterInspection */
        Request $request,
        /** @noinspection PhpUnusedParameterInspection */
        Response $response,
        /** @noinspection PhpUnusedParameterInspection */
        array $args)
    {
        // TODO Secretトークンは環境変数に入れる
        // Secretトークンで認証.
        $signature = 'sha1=' . hash_hmac('sha1', $request->getBody(), getenv('Github_Secret'));
        $isValidToken = hash_equals($request->getHeader('HTTP_X_HUB_SIGNATURE')[0], $signature);
        if (!$isValidToken) {
            return;
        }
        // ログ保存場所.
        $LOG_FILE = dirname(__FILE__) . '/../../logs/hook.log';
        // リクエストパラメータ受け取り.
        $requestParamPayload = $request->getParsedBody()['payload'];
        if (isset($requestParamPayload)) {
            $payload = json_decode($requestParamPayload, true);
            // どのブランチにプッシュしたか.
            if ($payload['ref'] === 'refs/heads/master') {
                // masterブランチ.
//                $output = shell_exec('sh '.dirname(__FILE__).'/../../deploy.sh master');
                file_put_contents($LOG_FILE, date("[Y-m-d H:i:s]") . " " . " git pulled master: " . $payload['head_commit']['message'] . PHP_EOL, FILE_APPEND | LOCK_EX);
//                file_put_contents($LOG_FILE, $output, FILE_APPEND|LOCK_EX);
                file_put_contents($LOG_FILE, exec('cd /var/www/html/YoutubeTypingGameGenerator') . PHP_EOL, FILE_APPEND | LOCK_EX);
                file_put_contents($LOG_FILE, exec('git pull origin master') . PHP_EOL, FILE_APPEND | LOCK_EX);
//                file_put_contents($LOG_FILE, exec('sudo composer intall') . PHP_EOL, FILE_APPEND|LOCK_EX);
//                file_put_contents($LOG_FILE, exec('cd vue-project') . PHP_EOL, FILE_APPEND|LOCK_EX);
//                file_put_contents($LOG_FILE, exec('npm install --production --no-save') . PHP_EOL, FILE_APPEND|LOCK_EX);
            } else if ($payload['ref'] === 'refs/heads/develop') {
                // developブランチ.
                $output = shell_exec('sh ' . dirname(__FILE__) . '/../../deploy.sh develop');
                //file_put_contents($LOG_FILE, $requestParamPayload . PHP_EOL, FILE_APPEND|LOCK_EX);
                file_put_contents($LOG_FILE, date("[Y-m-d H:i:s]") . " " . " git pulled develop: " . $payload['head_commit']['message'] . PHP_EOL, FILE_APPEND | LOCK_EX);
            } else {
                // それ以外.
                file_put_contents($LOG_FILE, date("[Y-m-d H:i:s]") . " invalid access: " . PHP_EOL, FILE_APPEND | LOCK_EX);
            }
        }
    }

}