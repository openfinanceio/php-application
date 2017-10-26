<?php
namespace CFX\Test;

class PDO {
    protected $testData = [];

    public function setTestData($key, array $data) {
        if (count($data) > 0 && !is_numeric(implode('', array_keys($data)))) throw new \RuntimeException("Test data should be an array of data arrays. For example: `setTestData('SELECT * FROM....', [['name' => 'Jim', 'dob' => '12345']])`.");
        if (!array_key_exists($key, $this->testData)) $this->testData[$key] = [];
        $this->testData[$key][] = $data;
        return $this;
    }
    protected function getTestData($key) {
        if (!array_key_exists($key, $this->testData)) $this->testData[$key] = [];
        if (count($this->testData[$key]) == 0) throw new \RuntimeException("Programmer: You need to set test data for `$key` using the `setTestData` method of your test datasource.");
        
        $data = array_pop($this->testData[$key]);

        if ($data instanceof \Exception) throw $data;

        return $data;
    }



    public function prepare($query) {
        $data = $this->getTestData($query);
        return new PDOStatement($this, $query, $data);
    }

    public function setLastInsertId($id) {
        $this->lastInsertId = $id;
    }

    public function lastInsertId($name=null) {
        $id = $this->lastInsertId;
        $this->lastInsertId = null;
        return $id;
    }
}

