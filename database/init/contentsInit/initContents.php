<?php

namespace database\init\contentsInit;

use Tests\Functional\E2EGameTest;
use Tests\Functional\E2EBaseTest;

////////////////////////////////
// 開発環境のみで動作          //
// 事前にseleniumを起動しておく//
// cd ~                       //
// ./start_selenium           //
////////////////////////////////

// 下記チャンネルID内の動画を自動で登録する.
// avex, AKB, hosino_gen


require __DIR__ . '/../../../vendor/autoload.php';

$settings = require __DIR__ . '/../../../src/settings.php';
// youtubeのAPI Key.
$youtubeApiKey = $settings['settings']['youtube_api']['key'];
// 取得する動画のチャンネルリスト.
$channelList=[
    'avex'=>'UC1oPBUWifc0QOOY8DEKhLuQ',
    'AKB'=>'UCxjXU89x6owat9dA8Z-bzdw',
    'hosino_gen'=>'UCPKlrgZXnnb89nSeITvTdGA',
    //'Victor'=>'UC4YT50DP7hbMsor8FmyEPMw',
    //'toysfactory'=>'UC4C37AMho6DRUIYnoc4fiSA'
];


foreach($channelList as $key => $channelId) {
    // チャンネルIDから動画IDを取得するAPI
    $url = 'https://www.googleapis.com/youtube/v3/search?' .
        'part=snippet' .                     // 出力形式（snippet:動画の基本的な情報）.
        '&channelId=' . $channelId . // チャンネルID.
        '&order=viewCount' .                 // 再生数の多い順にソート
        '&maxResults=50' .                   // 上位50個取得
        '&videoCaption=closedCaption' .      // 字幕のある動画のみフィルタ.
        '&type=video' .                      // 動画
        '&key=' . $youtubeApiKey;             // API key
    $json = file_get_contents($url);
    $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
    $arr = json_decode($json, true);
    //var_dump($arr);


    // 出力された動画の数.
    $videoNum = min(50, $arr['pageInfo']['totalResults']);
    for ($index = 0; $index < $videoNum; $index++) {
        //var_dump($arr['pageInfo']['totalResults']);
        var_dump($arr['items'][$index]['snippet']['title']);
        //var_dump($arr['items'][$index]['snippet']['thumbnails']['medium']['url']);
        //var_dump($arr['items'][$index]['id']['videoId']);
        $videoId = $arr['items'][$index]['id']['videoId'];
        E2EGameTest::$videoId = $videoId;
        E2EGameTest::$testMovieUrl = 'https://www.youtube.com/watch?v=' . $videoId;
        E2EBaseTest::setUpBeforeClass();
        //E2EBaseTest::$HOST_NAME="ytgg.murase-msk.work";
        $e2eGameTest = new E2EGameTest();
        $e2eGameTest->registerMovie();
        $e2eGameTest::$driver->close();
        // スリープ.
        sleep(3);
    }
}