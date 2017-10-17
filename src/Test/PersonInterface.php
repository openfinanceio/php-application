<?php
namespace CFX\Test;

interface PersonInterface extends \CFX\BaseResourceInterface {
    public function getName();
    public function getDob();
    public function getExists();
    public function getActive();
    public function getBestFriend();
    public function getFriends();

    public function setName($val=null);
    public function setDob($val=null);
    public function setExists($val=null);
    public function setActive($val=null);
    public function setBestFriend(PersonInterface $val=null);
    public function setFriends(\KS\JsonApi\ResourceCollectionInterface $val=null);
}

