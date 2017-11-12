<?php
namespace CFX;

interface RequestInterface extends \Psr\Http\Message\RequestInterface {

    /**
     * consumePathPart -- consume the next portion of the path, moving the index ahead by one
     *
     * @return string $pathPart The part of the path consumed
     * @throws PathOverconsumedException
     */
    public function consumePathPart();

    /**
     * getCurrentPathPosition -- get the current position of the consumed path part
     *
     * This is allows you to give appropriate error messages when problems are found in the path
     *
     * @return int $position The position of the last consumed path part. If no part has been consumed, returns 0
     */
    public function getCurrentPathPosition();

    /**
     * peekPathPart -- see what the next path part is without actually consuming it
     *
     * Useful for paths with optional arguments, etc.
     */
    public function peekPathPart();
}

