<?php
namespace CFX\Test;

class Person extends \CFX\AbstractDataObject implements PersonInterface {
    protected $resourceType = 'people';
    protected $attributes = [
        'name' => null,
        'dob' => null,
        'exists' => true,
        'active' => true,
    ];
    protected $relationships = ['friends', 'bestFriend'];

    public function getName() { return $this->attributes['name']; }
    public function getDob() { return $this->attributes['dob']; }
    public function getExists() { return $this->attributes['exists']; }
    public function getActive() { return $this->attributes['active']; }
    public function getBestFriend() { return $this->relationships['bestFriend']->getData(); }
    public function getFriends() { return $this->relationships['friends']->getData(); }

    public function setName($val=null) {
        $this->attributes['name'] = $val;
        return $this;
    }

    public function setDob($val=null) {
        $this->attributes['dob'] = $val;
        return $this;
    }

    public function setExists($val=null) {
        $this->attributes['exists'] = $val;
        return $this;
    }

    public function setActive($val=null) {
        $this->attributes['active'] = $val;
        return $this;
    }

    public function setBestFriend(PersonInterface $val=null) {
        $this->relationships['bestFriend']->setData($val);
        return $this;
    }

    public function setFriends(\KS\JsonApi\ResourceCollectionInterface $val=null) {
        $this->relationships['friends']->setData($val);
        return $this;
    }





    public function save() {
        $this->db->save($this);
    }
}

