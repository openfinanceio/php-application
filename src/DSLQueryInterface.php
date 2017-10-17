<?php
namespace CFX;

interface DSLQueryInterface {
    public function getWhere();
    public function getParams();
    public function requestingCollection();
}

