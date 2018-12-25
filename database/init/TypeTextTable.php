<?php
/**
 * Created by PhpStorm.
 * User: masaki
 * Date: 2018/12/03
 * Time: 19:53
 */

namespace database\init;


class TypeTextTable extends BaseTable
{
    // カラム名.
    public const ID = 'id';
    public const TYPE_TEXT = 'type_text';
    public const VIDEO_CODE = 'video_code';
    public const TITLE = 'title';
    public const THUMBNAIL = 'thumbnail';
    public const LAST_UPDATE = 'last_update';
    /** @var string tableName テーブル名 */
    public const tableName = 'type_text_table';

    /** @var \PDO $con データベースコネクション */
    private $con;
    /** @var string createTableSql  */
    private const createTableSql =
        "create table if not exists ".self::tableName."("
        .self::ID." serial NOT NULL, "
        .self::TYPE_TEXT." jsonb NOT NULL, "
        .self::VIDEO_CODE." text, "
        .self::TITLE." text, "
        .self::THUMBNAIL." text, "
        .self::LAST_UPDATE." timestamp without time zone, "
        ."CONSTRAINT type_text_table_id_primary_key PRIMARY KEY (".self::ID.")"
        .") ";

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
        //echo $data['type_text'].PHP_EOL;
        $sql = 'insert into '.self::tableName
            .' ('
            .self::TYPE_TEXT.', '
            .self::VIDEO_CODE.', '
            .self::TITLE.', '
            .self::THUMBNAIL.', '
            .self::LAST_UPDATE
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
}