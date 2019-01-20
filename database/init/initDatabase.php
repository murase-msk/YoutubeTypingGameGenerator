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
// 削除
$accountTable->deleteTable();
// 作成順
$accountTable->createTable();

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
$typeTextTable = new TypingGameTable($con);
// 削除
$typeTextTable->deleteTable();
// 作成
$typeTextTable->createTable();


// シードデータインサート.
// accountテーブルデータ
$seeds = require __DIR__ . '/../seeds/typingGameSeed.php';
foreach ($seeds as $key => $value) {
    $typeTextTable->insertData(
        [
            TypingGameTable::TYPE_TEXT => $value[TypingGameTable::TYPE_TEXT],
            TypingGameTable::VIDEO_ID => $value[TypingGameTable::VIDEO_ID],
            TypingGameTable::TITLE => $value[TypingGameTable::TITLE],
            TypingGameTable::THUMBNAIL => $value[TypingGameTable::THUMBNAIL]
        ]);
}

