<?php
namespace CFX\Test;

interface DataContextInterface {
    public function getDb($key='default');
    public function executeQuery(\CFX\SQLQueryInterface $query);
}



