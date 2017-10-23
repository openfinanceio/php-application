<?php
namespace CFX;

interface RequestInterface extends \Psr\Http\Message\RequestInterface {
    public function consumePathPart();
}

