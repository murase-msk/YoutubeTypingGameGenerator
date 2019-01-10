<?php
/**
 * Created by PhpStorm.
 * User: masaki
 * Date: 2018/09/16
 * Time: 15:22
 */

namespace Tests\Functional;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use PDO;

class E2EBaseTest extends \PHPUnit\Framework\TestCase
{
    /** @var RemoteWebDriver */
    public static $driver;
    /** @var array */
    protected static $settings;
    /** PDO */
    protected static $pdo;

    /** ホスト名 */
    public static $HOST_NAME = 'localhost';

    public static function setUpBeforeClass()
    {
        // 設定読み込み.
        $settings = require __DIR__ . '/../../src/settings.php';
        self::$settings = $settings['settings'];

        // DB接続.
        $db = self::$settings['db'];
        self::$pdo = new PDO('pgsql:host=' . $db['host'] . ';dbname=' . $db['dbname'], $db['user'], $db['pass']);
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // selenium
        $host = 'http://localhost:4444/wd/hub';
        // chrome ドライバーの起動
        self::$driver = RemoteWebDriver::create($host, DesiredCapabilities::chrome(),120000,120000);
    }

    public static function tearDownAfterClass()
    {

        // ブラウザを閉じる
        self::$driver->close();
    }


    /**
     * @test
     */
    public function dummy()
    {
        $this->assertEquals('', '');
        //$this->markTestSkipped();
    }
//    protected static function initApp()
//    {
//        session_start();
//        // Instantiate the app
//        $settings = require __DIR__ . '/../../src/settings.php';
//        self::$app = new \Slim\App($settings);
//        self::$settings = $settings['settings'];
//
//        // Set up dependencies
//        require __DIR__ . '/../../src/dependencies.php';
//        // Register middleware
//        require __DIR__ . '/../../src/middleware.php';
//        // validation.
//        require __DIR__.'/../../src/validation.php';
//        // Register routes
//        require __DIR__ . '/../../src/routes.php';
//        // Run app
//        self::$app->run();
//    }
}