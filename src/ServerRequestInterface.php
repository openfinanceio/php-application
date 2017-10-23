<?php
namespace CFX;

interface ServerRequestInterface extends RequestInterface, \Psr\Http\Message\ServerRequestInterface {
    public function authenticate();
    public function isAuthenticated();
}

