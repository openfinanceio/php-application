<?php
namespace CFX\SDK\Test;

class UsersClient extends \CFX\SDK\BaseSubclient {
    protected static $resourceType = 'users';

    protected function inflateData(array $data, $isCollection) {
        $f = $this->cfxClient->getFactory();

        if (!$isCollection) $data = [$data];
        foreach($data as $k => $o) $data[$k] = $f->newUser($o);
        return $isCollection ?
            $f->newJsonApiResourceCollection($data) :
            $data[0]
        ;
    }
}

