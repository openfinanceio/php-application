<?php
namespace CFX;

/**
 * Sadly, we're not specifying any return values or type hinting for parameters here because we messed up
 * definitions for resources and don't want to add unnecessary dependencies....
 */
interface ServerRequestInterface extends RequestInterface, \Psr\Http\Message\ServerRequestInterface {
    /**
     * Get the API key associated with this request
     *
     * @return \CFX\Brokerage\ApiKey|null
     */
    public function getApiKey();

    /**
     * Set the API key associated with this request
     *
     * @param \CFX\Brokerage\ApiKey $val
     * @return \CFX\ServerRequestInterface
     */
    public function setApiKey($val);
}

