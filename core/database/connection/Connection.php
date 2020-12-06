<?php
namespace core\database\connection;

// 链接的基础类
class Connection
{
    /**
     * @var object
     */
    protected object $pdo;

    /**
     * @var string
     */
    protected string $tablePrefix;

    /**
     * @var array
     */
    protected array $config;

    /**
     * Connection constructor.
     * @param $pdo
     * @param $config
     */
    public function __construct($pdo, $config)
    {
        $this->pdo = $pdo;
        $this->tablePrefix = $config['prefix'];
        $this->config = $config;
    }
}
