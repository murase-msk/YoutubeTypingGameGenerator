<?php

namespace src\Model\typingGame;

use Goutte\Client;
use Benlipp\SrtParser\Parser;
use PhpParser\Node\Expr\Array_;

/**
 * 歌詞サイトからスクレイピング
 */
class ScrapingLyrics
{
    public $crawler;
    // 歌詞サイト
    public const LYRICS_SEARCH_WEB_SITE = 'http://j-lyric.net/';

    function __construct()
    {
    }

    /**
     * タイトルを検索して必要なデータを取得
     *
     * @param String $title
     * @return void
     */
    function findLyricsResult(String $title) : Array
    {
        return $this->getCrawlerResult($title);
    }

    /**
     * タイトルを検索して必要なデータを取得
     *
     * @param String $title
     * @return void
     */
    private function getCrawlerResult(String $title) : Array
    {
        $client = new Client();

        // URLアクセス
        $crawler = $client->request('GET', self::LYRICS_SEARCH_WEB_SITE);
        // テキストボックス入力(フォーム入力)
        $form = $crawler->filter('#ebox form')->form();
        //$form = $crawler->selectButton('search')->form();
        $form['key'] = $title;
        $uri = $form->getUri();
        $uri = str_replace('&key','&kt',$uri);// keyをktに変換する(スクレイピング対策されている？).

        //画面遷移
        $crawler = $client->request('GET', $uri);
        // 検索結果を取得.
        $searchResult = $crawler->filter('#mnb')->children('.bdy')
             ->each(
                 function($node, $i){
                     return $node;
                 });
        // 検索結果のデータが入る（タイトル、歌詞ページURL、アーティスト名、歌詞の出だし）.
        $searchResultInfo = array();
        for($i=0; $i<count($searchResult); $i++){
            // タイトル.
            $foundTitle = $searchResult[$i]->filter('.mid > a')->text();
            // 歌詞ページのURL.
            $foundUrl = $searchResult[$i]->filter('.mid > a')->link()->getUri();
            // アーティスト名.
            $foundArtistName = $searchResult[$i]->filter('.sml')->eq(0)->filter('a')->text();
            // 歌詞の出だし.
            $foundIntroText = $searchResult[$i]->filter('.sml')->eq(1)->text();
            array_push($searchResultInfo, array(
                'foundTitle'=> $foundTitle, 
                'foundUrl'=> $foundUrl, 
                'foundArtistName' => $foundArtistName, 
                'foundIntroText' => $foundIntroText));
        }
        return $searchResultInfo;
    }
}
