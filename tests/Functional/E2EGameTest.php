<?php
/**
 * Created by PhpStorm.
 * User: masaki
 * Date: 2019/01/03
 * Time: 16:10
 */

namespace Tests\Functional;

use Facebook\WebDriver\Exception\WebDriverCurlException;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use FaceBook\Webdriver\Exception\NoSuchElementException;
use src\Model\TypingGameModel;

class E2EGameTest extends E2EBaseTest
{

    public static $videoId = 'MVW3-K0rf-I';
    public static $videoTitle = '【ご注文はうさぎですか？？】めぐがナンパしている';
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
     * 動画の登録
     */
    public function registerMovie()
    {
        // 指定URLへ遷移
        self::$driver->get('http://' . self::$HOST_NAME . '/');

        $element = self::$driver->findElement(WebDriverBy::name('youtube_url'));
        $element->sendKeys(self::$testMovieUrl);
        $element->submit();
        $driver = self::$driver;
        self::$driver->wait(50, 1000)->until(
            function() use($driver){
                // URLに"content1/watch"が含まれるようになったら終わり(画面が遷移するまで待つ).
                if(strpos($driver->getCurrentURL(), 'content1/watch') !== false) {
                    return  true;
                }
                try {
                    $driver->findElement(WebDriverBy::className('label-danger'));
                    // 画面遷移せず且つ、警告が出ている・・・待たずに終了.
                    //var_dump('警告出ています');
                    return true;
                }catch(NoSuchElementException $e){
                    // 画面遷移せず且つ、警告が出ていない・・・待ち.
                    //var_dump('警告出ていません'.PHP_EOL);
                    return false;
                }
            });
    }

    /**
     * @test
     * @testdox 動画登録後の画面遷移ができているか
     */
    public function windowsTransitionAfterRegisterMovie(){
        self::registerMovie();
        // 登録後の画面へ遷移できたか.
        $pattern = '@' . 'https?://' . str_replace('.', '\.', self::$HOST_NAME) . '/content1/watch/' . self::$videoId . '@';
        $this->assertRegExp($pattern, self::$driver->getCurrentURL());
        $element = self::$driver->findElement(WebDriverBy::id('type_start'));
        $this->assertContains('start', $element->getText());
        // TODO:登録しようとした動画が登録されていたらすでに登録されていますと表示して画面遷移する
        //...
    }

    /**
     *
     * @test
     * @testdox ゲーム開始
     * @depends windowsTransitionAfterRegisterMovie
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
     * @depends windowsTransitionAfterRegisterMovie
     */
    public function editTypeText(){
        self::$driver->get('http://'.self::$HOST_NAME.'/content1/watch/'.self::$videoId);
        // 編集ボタンを押して編集画面へ.
        $element = self::$driver->findElement(WebDriverBy::id('editText'));
        $element->click();
        // 編集画面へ遷移できたか.
        $this->assertEquals('http://'.self::$HOST_NAME.'/content1/edit/'.self::$videoId, self::$driver->getCurrentURL());

        // テキストを編集する.
        $element = self::$driver->findElement(
            WebDriverBy::id('phrase0'))->findElement(
                WebDriverBy::className('inputText'))->findElement(
                    WebDriverBy::tagName('input'));
        $element->clear();
        $element->sendKeys('きみかわいいね');
        // 保存.
        self::$driver->findElement(WebDriverBy::name('saveTypeInfo'))->submit();
        // 保存したらタイピング画面へ遷移できたか.
        $this->assertEquals('http://'.self::$HOST_NAME.'/content1/watch/'.self::$videoId, self::$driver->getCurrentURL());

        // 編集後のタイピング画面で反映されているか確認
        $element = self::$driver->findElement(WebDriverBy::id('type_start'));
        $element->click();
        self::$driver->wait(10)->until(
        // タイピング用のテキストが表示されるまで待つ.
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::className('restText'))
        );
        $element->sendKeys('kimikawaiine');
        $this->assertEquals(
            'きみかわいいね',
            self::$driver->findElement(WebDriverBy::className('inputtedText'))->getText()
        );
    }

    /**
     * @test
     * @testdox 登録動画一覧
     * @depends windowsTransitionAfterRegisterMovie
     */
    public function listMovie(){
        self::$driver->get('http://'.self::$HOST_NAME.'/content1/list');
        // 登録一覧に動画が登録されているか.
        $elements = self::$driver->findElements(WebDriverBy::className('thumbnail'));
        $matchFlg = false;  //登録一覧に動画が登録されているかのフラグ.
        foreach($elements as $element){
            $getText = $element->findElement(WebDriverBy::className('movieTitle'))->getText();
            if($getText === self::$videoTitle){
                $matchFlg = true;
                // 登録されたタイトルの動画があればクリックして正しく遷移するか.
                $element->findElement(WebDriverBy::tagName('a'))->click();
                $this->assertEquals('http://'.self::$HOST_NAME.'/content1/watch/'.self::$videoId, self::$driver->getCurrentURL());
                break;
            }
        }
        $this->assertEquals(true, $matchFlg);
    }

}