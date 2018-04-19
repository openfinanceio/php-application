<?php
namespace CFX;

interface ConfigInterface extends \KS\WebappConfigInterface {
    public function getDisplayErrors();
    public function getErrorLevel();
}

