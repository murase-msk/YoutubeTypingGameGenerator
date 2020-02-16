<?php

namespace src\Model\typingGame;

use Goutte\Client;
use Benlipp\SrtParser\Parser;
use PhpParser\Node\Expr\Array_;

/**
 * スクレイピングでYoutubeの情報を取得
 * Class ScrappingTypeText
 * @package src\Model\typingGame
 */
class ScrappingTypeText
{

    // youtubeのURL.
    public $youtubeUrl = '';
    public $crawler;
    public $videoCode;

    public function __construct($videoId)
    {
        $this->youtubeUrl = 'https://www.youtube.com/watch?v=' . $videoId;
//        $this->crawler = $this->getCrawlerResult($this->youtubeUrl);
        $this->videoCode = $videoId;
    }

    //     return $captionData;
    // }

    /**
     * XMLのダウンロードURLを配列データに変換する
     * @param string $downloadSubUrl 字幕ダウンロードのURL
     * @param string $yahooApiKey Yahoo APIのAPI Key
     * @return array 左のような形式で返す [0=>['startTime'=>xxx, 'endTime'=>xxx, 'text'=>xxx, 'Furigana'=>xxx,], 1=>[...], ...]
     */
    public function convertToArrayDataFromXML(string $downloadSubUrl, string $yahooApiKey): array
    {
        //動画IDがなければ404, 字幕データがなければ空白
        $html = file_get_contents($downloadSubUrl);
        if ($html === false) {    // 404 not found 動画IDがない.
            return array();
        } elseif ($html == "") { // 空のHTML（歌詞データがない）.
            return array();
        }
        $xml = simplexml_load_string($html);
        // $json = json_encode($xml);
        // $array = json_decode($json, TRUE);
        $captionData = array();
        $loopNum = 0;
        foreach ($xml->text as $key => $value) {
            $aaa=(string)$value[0];
            $bbb=(double)$value[0]["start"];
            $ccc=(double)$value[0]["dur"];
            array_push(
                $captionData,
                array(
                    'index' => $loopNum++,
                    'startTime' => (double)$value[0]["start"],
                    'endTime' => (double)$value[0]["start"]+(double)$value[0]["dur"],
                    'text' => (string)$value[0],
                    'Furigana' => ConvertTypeText::convertToHiragana((string)$value[0], $yahooApiKey)
                )
            );
        }

        return $captionData;
    }


    /**
     * YoutubeDataAPIからデータを取得.
     * @param string $youtubeApiKey Youtube のAPI key
     * @return array
     */
    public function getYoutubeData(string $youtubeApiKey): array
    {
        $videoId = $this->videoCode;
        // YoutubeAPIのAPIkey
        $url = 'https://www.googleapis.com/youtube/v3/videos?id=' . $videoId . '&key=' . $youtubeApiKey . '&fields=items(id,snippet(channelTitle,title,thumbnails),statistics)&part=snippet,contentDetails,statistics';
        $json = file_get_contents($url);
        $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $arr = json_decode($json, true);
        return array('title' => $arr['items'][0]['snippet']['title'], 'thumbnail' => $arr['items'][0]['snippet']['thumbnails']['medium']['url']);
    }
}
