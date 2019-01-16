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
use database\init\TypeTextTable;
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
        // inner join type_text_table on bookmark.type_text_id = type_text_table.id and type_text_table.video_code = 'jhOVibLEDhA'
        $bookmark = BookmarkTable::tableName;
        $account = AccountTable::tableName;
        $typeText = TypeTextTable::tableName;
        $sql = "select count(*) from ${bookmark}"
            ." inner join ${account} on ${bookmark}.".BookmarkTable::ACCOUNT_ID." = ${account}.".AccountTable::ID
            ." and ${account}.".AccountTable::ACCOUNT_NAME." = :accountName "
            ." inner join ${typeText} on ${bookmark}.".BookmarkTable::TYPE_TEXT_ID." = ${typeText}.".TypeTextTable::ID
            ." and ${typeText}.".TypeTextTable::VIDEO_CODE." = :videoId ";
        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':accountName', $accountName);
        $stmt->bindParam(':videoId', $videoId);
        $stmt->execute();
        $result = $stmt->fetchColumn(0);
        return (int)$result === 0 ? false: true;
    }

    /**
     * AccountテーブルからIDを取得する
     * @param string $accountName
     * @return int
     */
    public function getAccountIdFromName(string $accountName): int
    {
        $sql = 'select '.AccountTable::ID.' '
            .' from '.AccountTable::tableName.' where '.AccountTable::ACCOUNT_NAME.' = :account ';
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
        $sql = 'select '.TypeTextTable::ID.' '
            .' from '.TypeTextTable::tableName.' where '.TypeTextTable::VIDEO_CODE.' = :videoId ';
        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':videoId', $videoId);
        $stmt->execute();
        $accountId = $stmt->fetch()[TypeTextTable::ID];
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
        $typeTextId = $this->getTypeTextIdFromVideoId($videoId);
        $sql = 'insert into '.BookmarkTable::tableName.' ('
            .BookmarkTable::ACCOUNT_ID.', '
            .BookmarkTable::TYPE_TEXT_ID
            .') values(:account_id, :type_text_id)';
        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':account_id', $accountId);
        $stmt->bindParam(':type_text_id', $typeTextId);
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
        $sql = 'delete from '.BookmarkTable::tableName
            .' where '.BookmarkTable::ACCOUNT_ID.' = :accountId and '.BookmarkTable::TYPE_TEXT_ID.' = :typeTextId';
        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':accountId', $accountId);
        $stmt->bindParam(':typeTextId', $typeTextId);
        $stmt->execute();
    }
}