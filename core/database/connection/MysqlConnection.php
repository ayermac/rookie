<?php
namespace core\database\connection;

use core\database\query\MysqlGrammar;
use core\database\query\QueryBuilder;

// 继承基础类
class MysqlConnection extends Connection
{

    /**
     * @var object
     */
    protected static object $connection;

    public function getConnection()
    {
        return self::$connection;
    }

    /**
     * SQL查询
     * @param string $sql SQL语句
     * @param array $bindings 绑定
     * @param bool $useReadPdo 是否使用只读
     * @return mixed
     */
    public function select($sql, $bindings = [], $useReadPdo = true)
    {
        $statement = $this->pdo;
        $sth = $statement->prepare($sql);
        try {
            $sth->execute($bindings);
            return $sth->fetchAll();
        } catch (\PDOException $exception) {
            echo($exception->getMessage());
        }
    }

    /**
     * SQL查询
     * @param string $sql SQL语句
     * @param array $bindings 绑定
     * @param bool $useReadPdo 是否使用只读
     * @return mixed
     */
    public function find($sql, $bindings = [], $useReadPdo = true)
    {
        $statement = $this->pdo;
        $sth = $statement->prepare($sql);
        try {
            $sth->execute($bindings);
            return $sth->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $exception) {
            echo($exception->getMessage());
        }
    }


    /**
     * 调用不存在的方法 调用一个新的查询构造器
     * @param string $method 调用方法
     * @param string $parameters 参数
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        // 返回QueryBuilder类
        return $this->newBuilder()->$method(...$parameters);
    }


    /**
     * 创建新的查询器
     * @return QueryBuilder
     */
    public function newBuilder()
    {
        return new QueryBuilder($this, new MysqlGrammar());
    }
}
