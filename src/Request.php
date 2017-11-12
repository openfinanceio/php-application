<?php
namespace CFX;

/**
 * Adapts Guzzle's PSR7 Request implementation. All documentation is taken from https://github.com/guzzle/psr7/blob/master/src/Request.php
 */
class Request implements RequestInterface
{
    use MessageTrait;




    /**
     * @param string                               $method  HTTP method
     * @param string|UriInterface                  $uri     URI
     * @param array                                $headers Request headers
     * @param string|null|resource|StreamInterface $body    Request body
     * @param string                               $version Protocol version
     */
    public function __construct(
        $method,
        $uri,
        array $headers = [],
        $body = null,
        $version = '1.1'
    ) {
        $r = static::getRequestClass();
        $this->r = new $r($method, $uri, $headers, $body, $version);
    }

    public function getRequestTarget()
    {
        return $this->r->getRequestTarget();
    }

    public function withRequestTarget($requestTarget)
    {
        $new = clone $this;
        $new->r = $new->r->withRequestTarget($requestTarget);
        return $new;
    }

    public function getMethod()
    {
        return $this->r->getMethod();
    }

    public function withMethod($method)
    {
        $new = clone $this;
        $new->r = $new->r->withMethod($method);
        return $new;
    }

    public function getUri()
    {
        return $this->r->getUri();
    }

    public function withUri(\Psr\Http\Message\UriInterface $uri, $preserveHost = false)
    {
        if ($this->r->getUri() === $uri) return $this;
        $oldPath = $this->getUri()->getPath();
        $new = clone $this;
        $new->r = $new->r->withUri($uri, $preserveHost);
        if ($oldPath != $uri->getPath()) {
            $new->pathIndex = 0;
        }
        return $new;
    }




    protected static function getRequestClass()
    {
        return "\\GuzzleHttp\\Psr7\\Request";
    }
}


