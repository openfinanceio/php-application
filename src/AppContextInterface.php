<?php
namespace CFX;

interface AppContextInterface extends \KS\ConfigInterface, \KS\JsonApi\ContextInterface {
    public function getDisplayErrors();
    public function getErrorLevel();
}

