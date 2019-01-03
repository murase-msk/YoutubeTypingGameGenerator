<?php
/**
 * Created by PhpStorm.
 * User: masaki
 * Date: 2018/12/16
 * Time: 17:05
 */

namespace src\Model;

use database\init\TypeTextTable;

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
     */
    public function searchTypeText($videoId)
    {
        $sql = 'select * from '.TypeTextTable::tableName.' where '.TypeTextTable::VIDEO_CODE.' = :videoId';
        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':videoId', $videoId);
        $stmt->execute();
        $result = json_decode($stmt->fetchAll()[0]['type_text']);
        return $result;

    }

    /**
     * 動画情報を取得
     */
    public function getAllVideoList()
    {
        $sql = 'select * from '.TypeTextTable::tableName;
        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }

    /**
     * すべてのVideoIdを取得する
     */
    public function listVideoId()
    {
        $sql = 'select '.TypeTextTable::VIDEO_CODE.' from '.TypeTextTable::tableName;
        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        return $result;

    }

    // 情報の更新.
    public function updateTypeInfo($videoId, $typeInfo)
    {
        $nowData = date("Y-m-d H:i:s");
        $sql = 'update '.TypeTextTable::tableName
            .' set '
            .' '.TypeTextTable::TYPE_TEXT.'=:type_text, '
            .' '.TypeTextTable::VIDEO_CODE.'=:video_code, '
            .' '.TypeTextTable::LAST_UPDATE.'=:last_update '
            .' '.'where '.TypeTextTable::VIDEO_CODE.' = \''.$videoId.'\'';
        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':type_text', $typeInfo);
        $stmt->bindParam(':video_code', $videoId);
        $stmt->bindParam(':last_update', $nowData);
        $stmt->execute();
    }

    /**
     * データ挿入
     * @param $data [type_text=>..., video_code=>..., title=>..., thumbnail=>...,]
     */
    public function insertData($data)
    {
        echo 'insert data' . PHP_EOL;
        $nowData = date("Y-m-d H:i:s");
        //echo $data['type_text'].PHP_EOL;
        $sql = 'insert into '.TypeTextTable::tableName
            .' ('
            .TypeTextTable::TYPE_TEXT.', '
            .TypeTextTable::VIDEO_CODE.', '
            .TypeTextTable::TITLE.', '
            .TypeTextTable::THUMBNAIL.', '
            .TypeTextTable::LAST_UPDATE
            .') '
            .'values(:type_text, :video_code, :title, :thumbnail, :last_update)';
        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':type_text', $data['type_text']);
        $stmt->bindParam(':video_code', $data['video_code']);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':thumbnail', $data['thumbnail']);
        $stmt->bindParam(':last_update', $nowData);
        $stmt->execute();
    }

    /**
     * 動画データ削除
     * @param String $videoId
     */
    public function delete($videoId)
    {
        $sql = 'delete from '.TypeTextTable::tableName.' where '.TypeTextTable::VIDEO_CODE.' = :videoId';
        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':videoId', $videoId);
        $stmt->execute();
    }
}