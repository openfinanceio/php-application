<?php
namespace CFX\Test;

class HttpClient extends \GuzzleHttp\Client {
    protected $nextResponse = [];
    protected $requestTrace = [];

    public function setNextResponse($r) {
        if (
            !($r instanceof \Psr\Http\Message\ResponseInterface) &&
            !($r instanceof \RuntimeException)
        ) {
            throw new \TypeError("First argument must be a \Psr\Http\Message\ResponseInterface or some kind of exception");
        }
        $this->nextResponse[] = $r;
        return $this;
    }

    public function getLastRequest() {
        $i = count($this->requestTrace);
        if ($i == 0) return null;
        return $this->requestTrace[$i-1];
    }

    public function send(\Psr\Http\Message\RequestInterface $request, array $options = []) {
        if (count($this->nextResponse) == 0) throw new \RuntimeException("This is a test HTTP Client that does not make real HTTP calls. You must set the response for the request you're about to execute by using the `setNextResponse(\GuzzleHttp\Message\ResponseInterface \$r)` method.");

        $this->requestTrace[] = $request;
        $res = array_shift($this->nextResponse);

        if ($res instanceof \Exception) {
            throw $res;
        }

        return $res;
    }
}

