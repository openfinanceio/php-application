<?php
namespace CFX\Test;

class DataContext extends \CFX\AbstractDataContext {
    protected static $resourceType = 'people';

    protected function initializeDatasource($name) {
        if ($name == 'people') return new PeopleDatasource($this);
    }
}

