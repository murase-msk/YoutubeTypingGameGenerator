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

    function __construct($videoId)
    {
        $this->youtubeUrl = 'https://www.youtube.com/watch?v=' . $videoId;
//        $this->crawler = $this->getCrawlerResult($this->youtubeUrl);
        $this->videoCode = $videoId;
    }

    // クローラーの結果.
    // private function getCrawlerResult($url)
    // {
    //     $client = new Client();

    //     // URLアクセス
    //     $crawler = $client->request('GET', 'https://downsub.com/');

    //     // テキストボックス入力(フォーム入力)
    //     $form = $crawler->selectButton('Download')->form();
    //     $form = $form->first()->form();
    //     //$form = $form->form();
    //     $form['url'] = $url;

    //     // ボタン押して画面遷移
    //     $crawler = $client->submit($form);
    //     $this->crawler = $crawler;
    //     return $crawler;
    // }

    /**
     * 取得できる言語リストを取得.
     * @return array 言語リストの配列([lang1, lang2 ,lang3....])
     */
    // public function getScriptLanguageList(): array
    // {
    //     // 字幕の言語（国名）を取得.
    //     $language = $this->crawler->filter('#show')->text();
    //     $language = explode('Or translate from English to:', $language);
    //     $language = explode('>>', $language[0]);
    //     array_shift($language);
    //     $languageList = array_map(function ($item) {
    //         return str_replace('Download   ', '', $item);
    //     }, $language);
    //     return $languageList;
    // }

    /**
     * 字幕ダウンロードサイトから字幕データのURLを取得
     * @param int $languageIndex ダウンロードページの上から何番目か
     * @return string ダウンロードURL
     */
    // public function getSrtUrl(int $languageIndex)
    // {
    //     // 字幕ファイル(srt)リンクのURL取得
    //     $subtitleUrl = $this->crawler
    //         ->filter('#show')
    //         ->children()
    //         ->filter('a')
    //         ->each(function ($node, $i) {
    //             return $node->selectLink('>>Download')->link()->getUri();
    //         });

    //     // ダウンロード
    //     $downloadSubUrl = $subtitleUrl[$languageIndex];
    //     return $downloadSubUrl;
    // }

    // /**
    //  * SrtファイルのダウンロードURLを配列データに変換する
    //  * @param string $downloadSubUrl 字幕ダウンロードのURL
    //  * @param string $yahooApiKey Yahoo APIのAPI Key
    //  * @return array 左のような形式で返す [0=>['startTime'=>xxx, 'endTime'=>xxx, 'text'=>xxx, 'Furigana'=>xxx,], 1=>[...], ...]
    //  */
    // public function convertToArrayDataFromSrtSubUrl(string $downloadSubUrl, string $yahooApiKey): array
    // {
    //     $downloadFilePath = '/tmp/subtitle.xml';
    //     $this->download($downloadSubUrl, $downloadFilePath);
    //     // srt処理.
    //     $captionData = array();
    //     try {
    //         $parser = new Parser();
    //         $parser->loadFile($downloadFilePath);
    //         $captions = $parser->parse();
    //         $loopNum = 0;
    //         foreach ($captions as $key => $caption) {
    //             array_push(
    //                 $captionData,
    //                 array(
    //                     'index' => $loopNum++,
    //                     'startTime' => $caption->startTime,
    //                     'endTime' => $caption->endTime,
    //                     'text' => $caption->text,
    //                     'Furigana' => ConvertTypeText::convertToHiragana($caption->text, $yahooApiKey)
    //                 )
    //             );
    //         }
    //         // ファイル削除.
    //         unlink($downloadFilePath);
    //     } catch (\Benlipp\SrtParser\Exceptions\FileNotFoundException $e) {
    //         $e->getMessage();
    //     }

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
        if($html === false){    // 404 not found 動画IDがない.
            return array();
        }else if($html == ""){ // 空のHTML（歌詞データがない）.
            return array();
        }
        $xml = simplexml_load_string($html);
        // $json = json_encode($xml);
        // $array = json_decode($json, TRUE);
        $captionData = array();
        $loopNum = 0;
        foreach($xml->text as $key => $value){
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

    // // ファイルをダウンロード.
    // private function download($url, $savePath)
    // {
    //     $fp = fopen($url, 'r');
    //     $fpw = fopen($savePath, 'w');
    //     $size = 0;

    //     while (!feof($fp)) {
    //         $buffer = fread($fp, 1024);
    //         if ($buffer === false) {
    //             $size = false;
    //             break;
    //         }
    //         $wsize = fwrite($fpw, $buffer);
    //         if ($wsize === false) {
    //             $size = false;
    //             break;
    //         }
    //         $size += $wsize;
    //     }

    //     fclose($fp);
    //     fclose($fpw);
    //     return $size;
    // }

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