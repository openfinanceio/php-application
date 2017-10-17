<?php
namespace CFX\Test;

class PDOStatement {
    protected $pdo;
    protected $testData;
    public $queryString;

    public function __construct(PDO $pdo, $query, $testData) {
        $this->pdo = $pdo;
        $this->queryString = $query;
        $this->testData = $testData;
    }

    public function execute($params=null) {
        if ($this->testData instanceof \Exception) throw $this->testData;
        if (strtolower(substr($this->queryString, 0, 6)) == 'insert') $this->pdo->setLastInsertId(md5(uniqid()));
        return true;
    }

    public function fetch() {
        return array_shift($this->testData);
    }

    public function fetchAll() {
        $data = $this->testData;
        $this->testData = null;
        return $data;
    }
}


