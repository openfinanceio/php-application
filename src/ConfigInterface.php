<?php
namespace CFX;

interface ConfigInterface extends \KS\ConfigInterface {
    public function getDisplayErrors();
    public function getErrorLevel();
}

