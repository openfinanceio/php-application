<?php
namespace CFX\SDK\Test;

class Client extends \CFX\SDK\BaseClient {
    protected static $apiName = 'tester';
    protected static $apiVersion = '1';

    public function getDatasources() {
        return $this->datasources;
    }

    public function instantiateSubclient($name) {
        if ($name == 'people') return new PeopleRestDatasource($this);

        return parent::instantiateSubclient($name);
    }
}

