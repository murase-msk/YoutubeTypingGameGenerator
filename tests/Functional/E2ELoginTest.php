<?php
/**
 * Created by PhpStorm.
 * User: masaki
 * Date: 2018/09/16
 * Time: 0:44
 */

namespace Tests\Functional;

use Facebook\WebDriver\WebDriverBy;
use src\Model\AccountModel;

/**
 * Class HomePageTest
 * @package Tests\Functional
 *
 */
class E2ELoginTest extends E2EBaseTest
{

    /** テストアカウント */
    protected const EMAIL = 'test@test.com';
    protected const ACCOUNT = 'testaccount';
    protected const PASS = 'test123';


    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass()
    {
        // 新規登録したアカウントの削除
        $accountModel = new AccountModel(self::$pdo);
        $accountModel->deleteAccount(self::ACCOUNT, self::PASS);

        parent::tearDownAfterClass();
    }

    /**
     *
     * @test
     * @testdox インデックスページ遷移
     */
    public function indexPage()
    {
        // 指定URLへ遷移
        self::$driver->get('http://' . self::$HOST_NAME . '/');
        $this->assertEquals('YTGG', self::$driver->getTitle());
    }

    /**
     *
     * @test
     * @testdox サインアップページ
     */
    public function signUpPage()
    {
        self::$driver->get('http://' . self::$HOST_NAME . '/');
        $element = self::$driver->findElement(WebDriverBy::id('sign_up_button'));
        $element->click();
        self::$driver->wait(3);
        $this->assertEquals('http://' . self::$HOST_NAME . '/signUp', self::$driver->getCurrentURL());
    }

    /**
     *
     * @test
     * @testdox 新規登録処理
     * @depends signUpPage
     */
    public function signUpCreateNewAccount()
    {
        // ID, PASS 入力.
        $element = self::$driver->findElement(WebDriverBy::name('email'));
        $element->sendKeys(self::EMAIL);
        $element = self::$driver->findElement(WebDriverBy::name('account'));
        $element->sendKeys(self::ACCOUNT);
        $element = self::$driver->findElement(WebDriverBy::name('password'));
        $element->sendKeys(self::PASS);
        $element = self::$driver->findElement(WebDriverBy::name('password2'));
        $element->sendKeys(self::PASS);
        // ログイン.
        $element->submit();
        self::$driver->wait(3);
//        // キャプチャ
//        $file = __DIR__ . '/' . "_chrome.png";
//        self::$driver->takeScreenshot($file);

        // 登録完了画面へ遷移できたか.
        $this->assertEquals('http://' . self::$HOST_NAME . '/registerAccount', self::$driver->getCurrentURL());
        $element = self::$driver->findElement(WebDriverBy::className('panel-body'));
        $this->assertContains('登録完了しました。', $element->getText());
        // トップページへ戻る.
        $element = $element->findElement(WebDriverBy::tagName('a'));
        $element->click();
        // 登録したアカウントになったか.
        $element = self::$driver->findElement(WebDriverBy::id('account_button'));
        $this->assertEquals(self::ACCOUNT, $element->getText());
    }

    /**
     *
     * @test
     * @testdox ログアウト処理
     * @depends signUpCreateNewAccount
     */
    public function logout()
    {
        // ログアウト.
        $element = self::$driver->findElement(WebDriverBy::id('logout_button'));
        $element->click();
        self::$driver->wait(3);
        // sign inのボタンが出たか.
        $element = self::$driver->findElement(WebDriverBy::id('sign_in_button'));
        $this->assertEquals('Sign In', $element->getText());
    }

    /**
     * @test
     * @testdox サインインページ遷移
     */
    public function signInPage()
    {
        self::$driver->get('http://' . self::$HOST_NAME . '/');
        $element = self::$driver->findElement(WebDriverBy::id('sign_in_button'));
        $element->click();
        self::$driver->wait(3);
        $this->assertEquals('http://' . self::$HOST_NAME . '/signIn', self::$driver->getCurrentURL());
    }

    /**
     * @test
     * @testdox ログイン処理
     * @depends signInPage
     */
    public function signIn()
    {
        $accountName = self::ACCOUNT;
        $password = self::PASS;
        // ID, PASS 入力.
        $element = self::$driver->findElement(WebDriverBy::id('InputText'));
        $element->sendKeys($accountName);
        $element = self::$driver->findElement(WebDriverBy::id('InputPassword'));
        $element->sendKeys($password);
        // ログイン.
        $element->submit();
        self::$driver->wait(3);
        // キャプチャ
//        $file = __DIR__ . '/' . "_chrome.png";
//        self::$driver->takeScreenshot($file);

        // トップページへ遷移できたか.
        $this->assertEquals('http://' . self::$HOST_NAME . '/', self::$driver->getCurrentURL());
        // root アカウントになったか.
        $element = self::$driver->findElement(WebDriverBy::id('account_button'));
        $this->assertEquals($accountName, $element->getText());
    }
}