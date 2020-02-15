<?php
/**
 * Created by PhpStorm.
 * User: masaki
 * Date: 2018/12/16
 * Time: 17:05
 */

namespace src\Model;

use database\init\TypingGameTable;

class TypingGameModel
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
     * video ID からタイピングテキストを取得
     * @param string $videoId
     * @return array
     */
    public function searchVideoInfo(string $videoId): array
    {
        $sql = 'select * from ' . TypingGameTable::tableName . ' where ' . TypingGameTable::VIDEO_ID . ' = :videoId';
        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':videoId', $videoId);
        $stmt->execute();
        $result = $stmt->fetchAll()[0];
        return $result;
    }

    /**
     * 指定数の動画情報を取得
     * @param int $page 何ページ目か
     * @param int $movieNum 取得する数
     * @return array 取得したデータの配列
     */
    public function getVideoList(int $page, int $movieNum): array
    {
        $offsetNum = ($page - 1) * $movieNum;
        $sql = 'select * from ' . TypingGameTable::tableName . ' order by id desc limit :movieNum offset :offsetNum ';
        $stmt = $this->con->prepare($sql);
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
     */
    public function isExistNextPageMovie(int $page, int $movieNum): bool
    {
        $totalVideoNum = $this->getTotalVideoNum();
        if ($page * $movieNum < $totalVideoNum) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 指定されたvideoIdが登録されているか
     * @param int $videoId
     * @return bool
     */
    public function isExistRegisteredVideo(string $videoId): bool
    {
        $sql = 'select * from ' . TypingGameTable::tableName . ' where '.TypingGameTable::VIDEO_ID.'=:video_id';
        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':video_id', $videoId);
        $stmt->execute();
        $fetchResult = $stmt->fetch();
        $result = !empty($fetchResult);
        return $result;
    }

    /**
     * 動画数を取得する
     */
    public function getTotalVideoNum(): int
    {
        $sql = 'select count(*) from ' . TypingGameTable::tableName;
        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchColumn(0);
        return $result;
    }

    /**
     * 情報の更新.
     * @param string $videoId
     * @param string $typeInfo
     */
    public function updateTypeInfo(string $videoId, string $typeInfo): void
    {
        $nowData = date("Y-m-d H:i:s");
        $sql = 'update ' . TypingGameTable::tableName
            . ' set '
            . ' ' . TypingGameTable::TYPE_TEXT . '=:type_text, '
            . ' ' . TypingGameTable::VIDEO_ID . '=:video_id, '
            . ' ' . TypingGameTable::LAST_UPDATE . '=:last_update '
            . ' ' . 'where ' . TypingGameTable::VIDEO_ID . ' = \'' . $videoId . '\'';
        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':type_text', $typeInfo);
        $stmt->bindParam(':video_id', $videoId);
        $stmt->bindParam(':last_update', $nowData);
        $stmt->execute();
    }

    /**
     * データ挿入
     * @param $data [type_text=>..., video_id=>..., title=>..., thumbnail=>...,]
     */
    public function insertData(array $data): void
    {
        echo 'insert data' . PHP_EOL;
        $nowData = date("Y-m-d H:i:s");
        //echo $data['type_text'].PHP_EOL;
        $sql = 'insert into ' . TypingGameTable::tableName
            . ' ('
            . TypingGameTable::TYPE_TEXT . ', '
            . TypingGameTable::VIDEO_ID . ', '
            . TypingGameTable::TITLE . ', '
            . TypingGameTable::THUMBNAIL . ', '
            . TypingGameTable::LAST_UPDATE
            . ') '
            . 'values(:type_text, :video_id, :title, :thumbnail, :last_update)';
        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':type_text', $data[TypingGameTable::TYPE_TEXT]);
        $stmt->bindParam(':video_id', $data[TypingGameTable::VIDEO_ID]);
        $stmt->bindParam(':title', $data[TypingGameTable::TITLE]);
        $stmt->bindParam(':thumbnail', $data[TypingGameTable::THUMBNAIL]);
        $stmt->bindParam(':last_update', $nowData);
        $stmt->execute();
    }

    /**
     * 動画データ削除
     * @param String $videoId
     */
    public function delete(string $videoId): void
    {
        $sql = 'delete from ' . TypingGameTable::tableName . ' where ' . TypingGameTable::VIDEO_ID . ' = :videoId';
        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':videoId', $videoId);
        $stmt->execute();
    }
}
