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

    public function __construct()
    {
    }

    /**
     * タイトルを検索して必要なデータを取得
     *
     * @param String $title
     * @return void
     */
    public function searchLyricsCandidateFromTitle(String $title) : array
    {
        $client = new Client();

        // URLアクセス
        $crawler = $client->request('GET', self::LYRICS_SEARCH_WEB_SITE);
        // テキストボックス入力(フォーム入力)
        $form = $crawler->filter('#ebox form')->form();
        //$form = $crawler->selectButton('search')->form();
        $form['key'] = $title;
        $uri = $form->getUri();
        $uri = str_replace('&key', '&kt', $uri);// keyをktに変換する(スクレイピング対策されている？).

        //画面遷移
        $crawler = $client->request('GET', $uri);
        // 検索結果を取得.
        $searchResult = $crawler->filter('#mnb')->children('.bdy')
             ->each(
                 function ($node, $i) {
                     return $node;
                 }
             );
        // 検索結果のデータが入る（タイトル、歌詞ページURL、アーティスト名、歌詞の出だし）.
        $searchResultInfo = array();
        for ($i=0; $i<count($searchResult); $i++) {
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

    /**
     * 歌詞を取得する
     *
     * @param string $url 歌詞ページ
     * @return array
     */
    public function getLyricsFromUrl(string $url): array
    {
        $client = new Client();

        // URLアクセス
        $crawler = $client->request('GET', $url);
        $lyrics = $crawler->filter('#Lyric')->html();
        // 歌詞を1行ずつ分割.
        $lyrics = explode("<br>", $lyrics);
        // 空白行を消す.
        $arrayNum = count($lyrics);
        for ($i =0; $i<$arrayNum; $i++) {
            if ($lyrics[$i] == '') {
                unset($lyrics[$i]);
            }
        }
        // indexを詰める.
        $lyrics = array_values($lyrics);
        return $lyrics;
    }

    /**
     * DB格納用のキャプションデータを生成する
     *
     * @param array $lyrics
     * @return array
     */
    public function createCaptionData(array $lyrics, string $yahooApiKey): array
    {
        $captionData = array();
        for ($i=0; $i<count($lyrics); $i++) {
            array_push(
                $captionData,
                array(
                    'index' => $i,
                    'startTime' => 0,
                    'endTime' => 0,
                    'text' => $lyrics[$i],
                    'Furigana' => ConvertTypeText::convertToHiragana((string)$lyrics[$i], $yahooApiKey)
                )
            );
        }
        return $captionData;
    }
}
