<?php
namespace CFX\Test;

class Factory extends \CFX\Factory {
    public function newPDO($dsn, $username=null, $password=null, $options=[]) { return new \PDO($dsn, $username, $password, $options); }

    public function newJsonApiResource($data=null, $type=null, $validAttrs=null, $validRels=null) {
        if ($type == 'users') return $this->newUser($data);
        if ($type !== null) throw new \KS\JsonApi\UnknownResourceTypeException("Type `$type` is unknown. You can handle this type by overriding the `newJsonApiResource` method in your factory and adding a handler for the type there.");
        return new \KS\JsonApi\GenericResource($this, $data, $validAttrs, $validRels);
    }

    public function newUser($data=null) {
        return new \CFX\Test\User($this, $data);
    }
}

