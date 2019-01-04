<?php
/**
 * Created by PhpStorm.
 * User: masaki
 * Date: 2018/12/02
 * Time: 22:43
 */

namespace src\Model\typingGame;


class ConvertTypeText
{

    public static $appid = '';

    public function __construct($sentence)
    {
    }

    // 連想配列で取得.
    // {'Surface'=>'...', 'Furigana'=>'...', 'Roman'=>'...', }.
    public static function convertSentence($sentence)
    {
        $url = 'https://jlp.yahooapis.jp/FuriganaService/V1/furigana?appid='.ConvertTypeText::$appid.'&sentence='.$sentence;

        $xml = simplexml_load_file($url); //XML ファイルの URL を指定
        $oneSentence = array('Surface'=>'', 'Furigana'=>'', 'Roman'=>'');
        foreach($xml->Result[0]->WordList[0] as $word){
            $oneSentence['Surface'] .=$word->Surface;
            if (isset($word->Furigana)) {
                $oneSentence['Furigana'] .= $word->Furigana;
                //$oneSentence['Roman'] .= $word->Roman;
            }else{
                //英数字以外削除する.
                $lowerStr=preg_replace('/[^a-z0-9\s]/', '',mb_strtolower($word->Surface));


                $oneSentence['Furigana'] .= $lowerStr;
                //$oneSentence['Roman'] .= mb_strtolower($word->Surface);
            }
        }
        return $oneSentence;
    }
}