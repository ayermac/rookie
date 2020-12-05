<?php

namespace core\database\connection;

// 继承基础类
class MysqlConnection extends Connection
{

    protected static $connection;

    public function getConnection()
    {
        return self::$connection;
    }

    // 执行sql
    public function findAll($sql, $bindings = [], $useReadPdo = true)
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


    // 调用不存在的方法 调用一个新的查询构造器
    public function __call($method, $parameters)
    {
        // 返回QueryBuilder类
        return $this->newBuilder()->$method(...$parameters);
    }


    // 创建新的查询器
    public function newBuilder()
    {
        return new QueryBuilder($this, new MysqlGrammar());
    }


}