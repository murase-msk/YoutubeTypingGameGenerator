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

// DB.
$settings = require __DIR__ . '/../../src/settings.php';
$settings = $settings['settings'];
$dbName = $settings['db']['dbname'];

$slimAppDb = new DatabaseConnection($dbName);
$con = $slimAppDb->connectDb();

// AccountTable初期化.

// テーブル初期化.
$accountTable = new AccountTable($con);
//$content1Table = new Content1Table($con);
// 削除順 content1, account.
//$content1Table->deleteTable();
$accountTable->deleteTable();
// 作成順 account, content1.
$accountTable->createTable();
//$content1Table->createTable();

// シードデータインサート.
// accountテーブルデータ
$seeds = require __DIR__ . '/../seeds/accountTableSeed.php';
foreach ($seeds as $key => $value) {
    $accountTable->insertData(
        [
            AccountTable::EMAIL => $value[AccountTable::EMAIL],
            AccountTable::ACCOUNT_NAME => $value[AccountTable::ACCOUNT_NAME],
            'password' => $value['password']
        ]);
}

// TypeTextTable初期化

// テーブル初期化.
$typeTextTable = new TypeTextTable($con);
// 削除
$typeTextTable->deleteTable();
// 作成
$typeTextTable->createTable();


// シードデータインサート.
// accountテーブルデータ
$seeds = require __DIR__ . '/../seeds/typeTextTableSeed.php';
foreach ($seeds as $key => $value) {
    $typeTextTable->insertData(
        [
            TypeTextTable::TYPE_TEXT => $value[TypeTextTable::TYPE_TEXT],
            TypeTextTable::VIDEO_CODE => $value[TypeTextTable::VIDEO_CODE],
            TypeTextTable::TITLE => $value[TypeTextTable::TITLE],
            TypeTextTable::THUMBNAIL => $value[TypeTextTable::THUMBNAIL]
        ]);
}

