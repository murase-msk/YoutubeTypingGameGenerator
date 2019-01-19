<?php
/**
 * Created by PhpStorm.
 * User: masaki
 * Date: 2019/01/01
 * Time: 15:53
 */

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use src\Model\TypingGame\ScrappingTypeText;

/**
 * ScrappingTypeTextのテスト
 * Class ScrapingTypeTextTest
 * @package Tests\Unit
 */
class ScrapingTypeTextTest extends TestCase
{
    /** 設定データ */
    protected static $settings;
    /** @var ScrappingTypeText $scrappingTypeText ScrappingTypeTextクラスのインスタンス */
    protected static $scrappingTypeText;
    /** youtube字幕の言語リスト */
    protected static $languageList;
    /** srtファイルのダウンロードURL */
    protected static $downloadSubUrl;

    public static function setUpBeforeClass()
    {
        // 設定読み込み.
        self::$settings = require __DIR__ . '/../../src/settings.php';
        $videoId = 'sr--GVIoluU';
        //$youtubeUrl = 'https://www.youtube.com/watch?v='.$videoId;

        self::initScrappingTypeText($videoId);
    }

    /**
     * 初期設定
     * @param $videoId
     */
    private static function initScrappingTypeText($videoId)
    {
        self::$scrappingTypeText = new ScrappingTypeText($videoId);
    }

    /**
     * @test
     * @testdox  youtube字幕の言語リストを取得
     */
    public function getScriptLanguageListTest()
    {

        self::$languageList = self::$scrappingTypeText->getScriptLanguageList();
//        var_dump(self::$languageList);
        $this->assertEquals('English', self::$languageList[0]);
        $this->assertEquals('Japanese', self::$languageList[1]);
        //$langListIndex = array_search('Japanese', $languageList);

    }

    /**
     * @test
     * @testdox  srtファイルのダウンロードURL取得
     */
    public function getSrtUrlTest()
    {
        $langListIndexEnglish = 0;//array_search('English', self::$languageList);//self::$languageList[0];
        $langListIndexJapanese = 1;//array_search('Japanese', self::$languageList);//self::$languageList[1];
        self::$downloadSubUrl = self::$scrappingTypeText->getSrtUrl($langListIndexJapanese);
        $downloadUrlLengthExpected = 529;
        $this->assertEquals($downloadUrlLengthExpected, strlen(self::$downloadSubUrl));
    }

    /**
     * @test
     * @testdox  SrtファイルのダウンロードURLを配列データに変換する
     * @depends getSrtUrlTest
     */
    public function convertToArrayDataFromSrtSubUrlTest()
    {
        // $captionData  = [0=>['startTime'=>xxx, 'endTime'=>xxx, 'text'=>xxx, 'Furigana'=>xxx,], 1=>[...], ...]
        $captionData = self::$scrappingTypeText->convertToArrayDataFromSrtSubUrl(
            self::$downloadSubUrl,
            self::$settings['settings']['yahoo_api']['key']
        );
        $this->assertEquals('U-U-U.S.A.', $captionData[0]['text']);
    }

    /**
     * @test
     * @testdox  YoutubeDataAPIからデータを取得.
     */
    public function getYoutubeDataTest()
    {
        // YoutubeDataAPIからタイトルとサムネイルのURLを取得.
        $youtubeData = self::$scrappingTypeText->getYoutubeData(self::$settings['settings']['youtube_api']['key']);
        $this->assertEquals('DA PUMP / U.S.A.', $youtubeData['title']);
    }
}