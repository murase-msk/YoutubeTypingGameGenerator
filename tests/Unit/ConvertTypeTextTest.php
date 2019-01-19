<?php
/**
 * Created by PhpStorm.
 * User: masaki
 * Date: 2019/01/01
 * Time: 15:19
 */

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use src\Model\typingGame\ConvertTypeText;

/**
 * Class ConvertTypeTextTest
 * @package Tests\Unit
 * @testdox ConvertTypeTextクラスのテスト
 */
class ConvertTypeTextTest extends TestCase
{

    /**
     *
     * @test
     * @testdox ひらがな変換テスト
     */
    public function convertToHiragana()
    {
        $settings = require __DIR__ . '/../../src/settings.php';
        $apiKey = $settings['settings']['yahoo_api']['key'];
        //漢字, ひらがな, 半角英数字, 半角全角記号, 半角スペース混じり
        $sentence = '正月 やすみ 「3日」or[4日]';
        $hiragana = ConvertTypeText::convertToHiragana($sentence, $apiKey);
        $this->assertEquals('しょうがつ やすみ 3にちor4にち', $hiragana);
        // TODO:全角英数字.
    }
}