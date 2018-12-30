<?php
/**
 * Created by PhpStorm.
 * User: masaki
 * Date: 2018/12/02
 * Time: 22:43
 */

namespace src\Model\typingGame;

/**
 * テキストを様々な形に変換するクラス
 * Class ConvertTypeText
 * @package src\Model\typingGame
 */
class ConvertTypeText
{

    public function __construct()
    {
    }

    /**
     * 文章をひらがなに変換
     * @param string $sentence
     *                 変換する文章
     * @param string $appId
     *                 Yahoo APIのAPI key
     * @return string
     *                ひらがなに変換した文字列（半角英数字はそのまま出力）
     */
    public static function convertToHiragana($sentence, $appId)
    {
        $url = 'https://jlp.yahooapis.jp/FuriganaService/V1/furigana?appid='.$appId.'&sentence='.$sentence;

        $xml = simplexml_load_file($url); //XML ファイルの URL を指定
        $oneSentence = array('Surface'=>'', 'Furigana'=>'', 'Roman'=>'');
        foreach($xml->Result[0]->WordList[0] as $word){
            $oneSentence['Surface'] .=$word->Surface;
            if (isset($word->Furigana)) {
                $oneSentence['Furigana'] .= $word->Furigana;
                //$oneSentence['Roman'] .= $word->Roman;
            }else{  // ふりがながなければ英数字なのでそのまま返す.
                //英数字以外削除する.
                $lowerStr=preg_replace('/[^a-z0-9\s]/', '',mb_strtolower($word->Surface));
                $oneSentence['Furigana'] .= $lowerStr;
                //$oneSentence['Roman'] .= mb_strtolower($word->Surface);
            }
        }
        return $oneSentence['Furigana'];
    }
}