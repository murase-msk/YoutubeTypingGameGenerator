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
 * Class TypeTextTable
 * @package database\init
 */
class BookmarkTable extends BaseTable
{
    // カラム名.
    public const ID = 'id';
    public const ACCOUNT_ID = 'account_id';
    public const TYPE_TEXT_ID = 'type_text_id';
    /** @var string tableName テーブル名 */
    public const tableName = 'bookmark';

    /** @var \PDO $con データベースコネクション */
    private $con;
    /** @var string createTableSql */
    private const createTableSql =
        "create table if not exists " . self::tableName . "("
        . self::ID . " serial NOT NULL, "
        . self::ACCOUNT_ID . " integer, "
        . self::TYPE_TEXT_ID . " integer, "
        . "CONSTRAINT bookmark_id_primary_key PRIMARY KEY (" . self::ID . "), "
        . "CONSTRAINT bookmark_account_id_foreign_key FOREIGN KEY (" . self::ACCOUNT_ID . ") REFERENCES " . AccountTable::tableName . "(" . AccountTable::ID . "), "
        . "CONSTRAINT bookmark_type_text_id_foreign_key FOREIGN KEY (" . self::TYPE_TEXT_ID . ") REFERENCES " . TypeTextTable::tableName . "(" . TypeTextTable::ID . ") "
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
        $sql = 'insert into ' . self::tableName
            . ' ('
            . self::ACCOUNT_ID . ', '
            . self::TYPE_TEXT_ID . ', '
            . ') '
            . 'values(:account_id, :type_text_id)';
        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':account_id', $data['account_id']);
        $stmt->bindParam(':type_text_id', $data['type_text_id']);
        $stmt->execute();
    }
}