<?php
/**
 * Created by PhpStorm.
 * User: masaki
 * Date: 2019/01/03
 * Time: 16:10
 */

namespace Tests\Functional;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use src\Model\TypingGameModel;

class E2EGameTest extends E2EBaseTest
{

    public static $videoId = 'MVW3-K0rf-I';
    public static $testMovieUrl = 'https://www.youtube.com/watch?v=';

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$testMovieUrl .= self::$videoId;
    }

    public static function tearDownAfterClass()
    {
        // テスト用で使用した動画を削除.
        $typingGameMode = new TypingGameModel(self::$pdo);
        $typingGameMode->delete(self::$videoId);

        parent::tearDownAfterClass();
    }

    /**
     *
     * @test
     * @testdox 動画の登録
     */
    public function registerMovie()
    {
        // 指定URLへ遷移
        self::$driver->get('http://'.self::$HOST_NAME.'/');

        $element = self::$driver->findElement(WebDriverBy::name('youtube_url'));
        $element->sendKeys(self::$testMovieUrl);
        $element->submit();
        self::$driver->wait(60);
        // 登録後の画面へ遷移できたか.
        $this->assertEquals('http://'.self::$HOST_NAME.'/content1/watch/'.self::$videoId, self::$driver->getCurrentURL());
        $element = self::$driver->findElement(WebDriverBy::id('type_start'));
        $this->assertContains('start', $element->getText());
        // TODO:登録しようとした動画が登録されていたらすでに登録されていますと表示して画面遷移する
    }

    /**
     *
     * @test
     * @testdox ゲーム開始
     * @depends registerMovie
     */
    public function startTypeGame()
    {
        // 指定URLへ遷移
        self::$driver->get('http://'.self::$HOST_NAME.'/content1/watch/'.self::$videoId);
        // スタートボタンでスタート.
        $element = self::$driver->findElement(WebDriverBy::id('type_start'));
        $element->click();
        self::$driver->wait(10)->until(
            // タイピング用のテキストが表示されるまで待つ.
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::className('restText'))
        );
        // キャプチャ
//        $file = __DIR__ . '/' . "_chrome.png";
//        self::$driver->takeScreenshot($file);
        // キー入力.
        $element->sendKeys('kikimikawaiine');
        $this->assertEquals(
            'ききみかわいいね',
            self::$driver->findElement(WebDriverBy::className('inputtedText'))->getText()
        );
        // 次のフレーズが出るまで待ち.
        self::$driver->wait(10)->until(
            WebDriverExpectedCondition::elementTextIs(
                WebDriverBy::className('restText'), 'いいいっしょにおちゃしていかない')
        );
        // キー入力.
        $element->sendKeys('iiissyoniotyasiteikanai');
        $this->assertEquals(
            'いいいっしょにおちゃしていかない',
            self::$driver->findElement(WebDriverBy::className('inputtedText'))->getText()
        );
    }

    /**
     * @test
     * @testdox 入力文字の編集
     * @depends registerMovie
     */
    public function editTypeText(){
        self::$driver->get('http://'.self::$HOST_NAME.'/content1/watch/'.self::$videoId);
        // TODO: 編集ボタンを押して編集画面へ.
        // TODO:編集結果が反映されている.
    }

    /**
     * @test
     * @testdox 登録動画一覧
     * @depends registerMovie
     */
    public function listMovie(){
        self::$driver->get('http://'.self::$HOST_NAME.'/content1/list');
        // TODO:動画が登録されている.
        // TODO:クリックしてタイピング画面へ遷移する.
    }

}