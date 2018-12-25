<?php

namespace src\Model\typingGame;

use Goutte\Client;
use Benlipp\SrtParser\Parser;

/**
 * スクレイピングでYoutubeの情報を取得
 * Class ScrappingTypeText
 * @package src\Model\typingGame
 */
class ScrappingTypeText
{

    // youtubeのURL.
    public $youtubeUrl = '';//'https://www.youtube.com/watch?v=sr--GVIoluU';
    public $crawler;
    public $videoCode;

    function __construct($url)
    {
        $this->youtubeUrl = $url;
        $this->crawler = $this->getCrawlerResult($url);
        $pattern = '/v=(.*?)(&|$)/';
        preg_match($pattern, $this->youtubeUrl, $matches);
        $this->videoCode = $matches[1];
    }

    // クローラーの結果.
    private function getCrawlerResult($url)
    {
        $client = new Client();

        // URLアクセス
        $crawler = $client->request('GET', 'https://downsub.com/');

        // テキストボックス入力(フォーム入力)
        $form = $crawler->selectButton('Download')->form();
        $form['url'] = $url;

        // ボタン押して画面遷移
        $crawler = $client->submit($form);
        $this->crawler = $crawler;
        return $crawler;
    }

    // 取得できる言語リストを取得.
    public function getScriptLanguageList()
    {
        // 字幕の言語（国名）を取得.
        $language = $this->crawler->filter('#show')->text();
        $language = explode('Or translate from English to:', $language);
        $language = explode('>>', $language[0]);
        array_shift($language);
        $languageList = array_map(function($item){return str_replace('Download   ','', $item);}, $language);
        return $languageList;
    }

    // 字幕ダウンロードサイトから字幕データのURLを取得
    public function getSrtUrl($languageIndex)
    {
        // 字幕ファイル(srt)リンクのURL取得
        $subtitleUrl = $this->crawler
            ->filter('#show')
            ->children()
            ->filter('a')
            ->each(function ($node, $i) {
                return $node->selectLink('>>Download')->link()->getUri();
            });

        // ダウンロード
        $downloadSubUrl = $subtitleUrl[$languageIndex];
        return $downloadSubUrl;
    }

    // 歌詞データ取得.
    public function downloadSrtFile($downloadSubUrl){
        $downloadFilePath = './subtitle.srt';
        $this->download($downloadSubUrl, $downloadFilePath);

        // srt処理.
        $captionData = array();
        try {
            $parser = new Parser();
            $parser->loadFile($downloadFilePath);
            $captions = $parser->parse();
            $loopNum=0;
            foreach($captions as $key=>$caption){
                array_push(
                    $captionData,
                    array(
                        'index'=>$loopNum++,
                        'startTime'=>$caption->startTime,
                        'endTime'=>$caption->endTime,
                        'text'=>$caption->text,
                        'Furigana'=>ConvertTypeText::convertSentence($caption->text)['Furigana']
                    )
                );
            }
        }catch(Exception $e){
            $e->getMessage();
        }

        return $captionData;
    }

    // ファイルをダウンロード.
    private function download($url, $savePath) {
        $fp = fopen($url, 'r');
        $fpw = fopen($savePath, 'w');
        $size = 0;

        while (!feof($fp)) {
            $buffer = fread($fp, 1024);
            if ($buffer === false) {
                $size = false;
                break;
            }
            $wsize = fwrite($fpw, $buffer);
            if ($wsize === false) {
                $size = false;
                break;
            }
            $size += $wsize;
        }

        fclose($fp);
        fclose($fpw);
        return $size;
    }

    // YoutubeDataAPIからデータを取得.
    public function getYoutubeData(){
        $videoId = $this->videoCode;
        // TODO: 環境変数に隠す
        $apiKey = '';
        $url = 'https://www.googleapis.com/youtube/v3/videos?id='.$videoId.'&key='.$apiKey.'&fields=items(id,snippet(channelTitle,title,thumbnails),statistics)&part=snippet,contentDetails,statistics';
        $json = file_get_contents($url);
        $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $arr = json_decode($json,true);
        return array('title'=>$arr['items'][0]['snippet']['title'], 'thumbnail'=>$arr['items'][0]['snippet']['thumbnails']['medium']['url']);
    }
}