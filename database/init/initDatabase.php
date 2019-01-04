<?php
/**
 * Created by PhpStorm.
 * User: masaki
 * Date: 2018/09/23
 * Time: 14:01
 */

namespace database\init;

require __DIR__ . '/../../vendor/autoload.php';

// マイグレーション.

// slim_app DB.
$settings = require __DIR__ . '/../../src/settings.php';
$dbName = 'slim_app';

$slimAppDb = new DatabaseConnection($dbName);
$con = $slimAppDb->connectDb();

// テーブル初期化.
$accountTable = new AccountTable($con);
$content1Table = new Content1Table($con);
// 削除順 content1, account.
$content1Table->deleteTable();
$accountTable->deleteTable();
// 作成順 account, content1.
$accountTable->createTable();
$content1Table->createTable();

// シードデータインサート.
// accountテーブルデータ
$seeds = require  __DIR__ . '/../seeds/accountTableSeed.php';
foreach($seeds as $key => $value){
    $accountTable->insertData(
        [
            'email'=>$value['email'],
            'account'=>$value['account'],
            'password'=>$value['password']
        ]);
}

// typing_game_auto_generator用.
$dbName = 'typing_game_auto_generator';

$DbConnection = new DatabaseConnection($dbName);
$con = $DbConnection->connectDb();

// テーブル初期化.
$typeTextTable = new TypeTextTable($con);
// 削除
$typeTextTable->deleteTable();
// 作成
$typeTextTable->createTable();


// シードデータインサート.
// accountテーブルデータ
$seeds = require  __DIR__ . '/../seeds/typeTextTableSeed.php';
foreach($seeds as $key => $value){
    $typeTextTable->insertData(
        [
            'type_text'=>$value['type_text'],
            'video_code'=>$value['video_code'],
            'title'=>$value['title'],
            'thumbnail'=>$value['thumbnail']
        ]);
}

