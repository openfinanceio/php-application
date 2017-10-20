<?php
namespace CFX;

interface AppInterface {
    /**
     * Run the API, executing the request provided and catching any exceptions thrown.
     *
     * @param ServerRequestInterface The ServerRequest to execute
     * @return ResponseInterface The response returned by executing the request
     */
    public function run(ServerRequestInterface $r);

    /**
     * Execute the provided request (without catching any exceptions)
     *
     * @param ServerRequestInterface The ServerRequest to execute
     * @return ResponseInterface The response returned by executing the request
     */
    public function executeRequest(ServerRequestInterface $r);
}

