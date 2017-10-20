<?php
namespace CFX;

interface AppContextInterface extends \KS\ConfigInterface, \KS\JsonApi\ContextInterface {
    public function getDisplayErrors();
    public function getErrorLevel();
    public function serverRequestFromGlobals();
    public function newRequest();
    public function newServerRequest();
    public function newResponse();
}

