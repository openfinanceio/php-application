<?php
namespace CFX\Test;

class Config extends \CFX\Config {
    public function getCFXPdos() {
        return $this->get('cfx-pdos');
    }
}

