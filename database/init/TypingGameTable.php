<?php
/**
 * Created by PhpStorm.
 * User: masaki
 * Date: 2018/12/03
 * Time: 19:53
 */

namespace database\init;

/**
 * タイピング情報を入ったテーブル
 * Class TypingGameTable
 * @package database\init
 */
class TypingGameTable extends BaseTable
{
    // カラム名.
    public const ID = 'id';
    public const TYPE_TEXT = 'type_text';
    public const VIDEO_ID = 'video_id';
    public const TITLE = 'title';
    public const THUMBNAIL = 'thumbnail';
    public const LAST_UPDATE = 'last_update';
    /** @var string tableName テーブル名 */
    public const tableName = 'typing_game';

    /** @var \PDO $con データベースコネクション */
    private $con;
    /** @var string createTableSql */
    private const createTableSql =
        "create table if not exists " . self::tableName . "("
        . self::ID . " serial NOT NULL, "
        . self::TYPE_TEXT . " jsonb NOT NULL, "
        . self::VIDEO_ID . " text, "
        . self::TITLE . " text, "
        . self::THUMBNAIL . " text, "
        . self::LAST_UPDATE . " timestamp without time zone, "
        . "CONSTRAINT typing_game_id_primary_key PRIMARY KEY (" . self::ID . "),"
        . "CONSTRAINT typing_game_video_id_unique UNIQUE (" . self::VIDEO_ID . ")"
        . ") ";

    public function __construct(\PDO $con)
    {
        parent::__construct($con, self::tableName, self::createTableSql);
        $this->con = $con;
    }

    /**
     * データ挿入
     * @param $data
     */
    public function insertData($data)
    {
        echo 'insert data' . PHP_EOL;
        $nowData = date("Y-m-d H:i:s");
        $sql = 'insert into ' . self::tableName
            . ' ('
            . self::TYPE_TEXT . ', '
            . self::VIDEO_ID . ', '
            . self::TITLE . ', '
            . self::THUMBNAIL . ', '
            . self::LAST_UPDATE
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
}