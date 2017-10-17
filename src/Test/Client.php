<?php
namespace CFX\SDK\Test;

class Client extends \CFX\SDK\BaseClient {
    protected static $apiName = 'tester';
    protected static $apiVersion = '1';
    protected $subclients = ['users'];

    public function getSubclients() {
        return $this->subclients;
    }

    public function instantiateSubclient($name) {
        if ($name == 'users') return new UsersClient($this);

        return parent::instantiateSubclient($name);
    }

    protected function createFactory() {
        return new \CFX\Test\Factory();
    }
}

