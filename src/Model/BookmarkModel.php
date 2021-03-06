<?php
/**
 * Created by PhpStorm.
 * User: masaki
 * Date: 2019/01/15
 * Time: 20:23
 */

namespace src\Model;

use database\init\AccountTable;
use database\init\BookmarkTable;
use database\init\TypingGameTable;
use phpDocumentor\Reflection\Types\Integer;

class BookmarkModel
{
    private $con;

    /**
     * SampleModel constructor.
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->con = $pdo;
    }

    /**
     * ブックマークしているか確認する
     * @param string $videoId videoId
     * @param string $accountName アカウント名
     * @return bool
     */
    public function isBookmark(string $accountName, string $videoId): bool
    {
        // 例
        // select count(*) from bookmark
        // inner join account on bookmark.account_id = account.id and account.account_name = 'root'
        // inner join typing_game on bookmark.typing_game_id = typing_game.id and typing_game.video_id = 'jhOVibLEDhA'
        $bookmark = BookmarkTable::tableName;
        $account = AccountTable::tableName;
        $typeText = TypingGameTable::tableName;
        $sql = "select count(*) from ${bookmark}"
            . " inner join ${account} on ${bookmark}." . BookmarkTable::ACCOUNT_ID . " = ${account}." . AccountTable::ID
            . " and ${account}." . AccountTable::ACCOUNT_NAME . " = :accountName "
            . " inner join ${typeText} on ${bookmark}." . BookmarkTable::TYPING_GAME_ID . " = ${typeText}." . TypingGameTable::ID
            . " and ${typeText}." . TypingGameTable::VIDEO_ID . " = :videoId ";
        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':accountName', $accountName);
        $stmt->bindParam(':videoId', $videoId);
        $stmt->execute();
        $result = $stmt->fetchColumn(0);
        return (int)$result === 0 ? false : true;
    }

    /**
     * ブックマーク済みの動画を取得する
     * @param string $accountName
     * @param $page
     * @param $movieNum
     * @return array
     */
    public function getBookmarkedVideoList(string $accountName, $page, $movieNum): array
    {
        $offsetNum = ($page - 1) * $movieNum;
        $bookmark = BookmarkTable::tableName;
        $account = AccountTable::tableName;
        $typeText = TypingGameTable::tableName;
        $sql = "select * from ${bookmark}"
            . " inner join ${account} on ${bookmark}." . BookmarkTable::ACCOUNT_ID . " = ${account}." . AccountTable::ID
            . " and ${account}." . AccountTable::ACCOUNT_NAME . " = :accountName "
            . " inner join ${typeText} on ${bookmark}." . BookmarkTable::TYPING_GAME_ID . " = ${typeText}." . TypingGameTable::ID
            . " order by ${$bookmark}." . BookmarkTable::ID . " desc limit :movieNum offset :offsetNum";
        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':accountName', $accountName);
        $stmt->bindParam(':movieNum', $movieNum);
        $stmt->bindParam(':offsetNum', $offsetNum);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }

    /**
     * 次のページに動画があるか
     * @param int $page 何ページ目か
     * @param int $movieNum 取得する数
     * @return bool 取得したデータの配列
     * @param string $accountName
     */
    public function isExistNextPageMovie(int $page, int $movieNum, string $accountName): bool
    {
        $totalVideoNum = $this->getTotalVideoNum($accountName);
        if ($page * $movieNum < $totalVideoNum) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * ブックマーク済みの動画数を取得する
     * @param string $accountName
     * @return int
     */
    public function getTotalVideoNum(string $accountName): int
    {
        $bookmark = BookmarkTable::tableName;
        $account = AccountTable::tableName;
        $typeText = TypingGameTable::tableName;
        $sql = "select count(*) from ${bookmark}"
            . " inner join ${account} on ${bookmark}." . BookmarkTable::ACCOUNT_ID . " = ${account}." . AccountTable::ID
            . " and ${account}." . AccountTable::ACCOUNT_NAME . " = :accountName "
            . " inner join ${typeText} on ${bookmark}." . BookmarkTable::TYPING_GAME_ID . " = ${typeText}." . TypingGameTable::ID;
        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':accountName', $accountName);
        $stmt->execute();
        $result = $stmt->fetchColumn(0);
        return $result;
    }


    /**
     * AccountテーブルからIDを取得する
     * @param string $accountName
     * @return int
     */
    public function getAccountIdFromName(string $accountName): int
    {
        $sql = 'select ' . AccountTable::ID . ' '
            . ' from ' . AccountTable::tableName . ' where ' . AccountTable::ACCOUNT_NAME . ' = :account ';
        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':account', $accountName);
        $stmt->execute();
        $accountId = $stmt->fetch()[AccountTable::ID];
        return (int)$accountId;
    }

    /**
     * Type_Text_tableテーブルからIDを取得する
     * @param string $videoId
     * @return int
     */
    public function getTypeTextIdFromVideoId(string $videoId): int
    {
        $sql = 'select ' . TypingGameTable::ID . ' '
            . ' from ' . TypingGameTable::tableName . ' where ' . TypingGameTable::VIDEO_ID . ' = :videoId ';
        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':videoId', $videoId);
        $stmt->execute();
        $accountId = $stmt->fetch()[TypingGameTable::ID];
        return (int)$accountId;
    }

    /**
     * ブックマークを登録する
     * @param $accountName
     * @param $videoId
     */
    public function registerBookmark($accountName, $videoId): void
    {
        $accountId = $this->getAccountIdFromName($accountName);
        $typingGameId = $this->getTypeTextIdFromVideoId($videoId);
        $sql = 'insert into ' . BookmarkTable::tableName . ' ('
            . BookmarkTable::ACCOUNT_ID . ', '
            . BookmarkTable::TYPING_GAME_ID
            . ') values(:account_id, :typing_game_id)';
        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':account_id', $accountId);
        $stmt->bindParam(':typing_game_id', $typingGameId);
        $stmt->execute();
    }

    /**
     * ブックマーク削除
     * @param string $accountName
     * @param string $videoId
     */
    public function deleteBookmark(string $accountName, string $videoId): void
    {
        $accountId = $this->getAccountIdFromName($accountName);
        $typeTextId = $this->getTypeTextIdFromVideoId($videoId);
        $sql = 'delete from ' . BookmarkTable::tableName
            . ' where ' . BookmarkTable::ACCOUNT_ID . ' = :accountId and ' . BookmarkTable::TYPING_GAME_ID . ' = :typeTextId';
        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':accountId', $accountId);
        $stmt->bindParam(':typeTextId', $typeTextId);
        $stmt->execute();
    }
}
