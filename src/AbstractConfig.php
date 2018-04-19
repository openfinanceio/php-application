<?php
namespace CFX;

abstract class AbstractConfig extends \KS\WebappConfig implements ConfigInterface {
    public function getDisplayErrors() { return $this->get('php-display-errors'); }
    public function getErrorLevel() { return $this->get('php-error-level'); }
}

