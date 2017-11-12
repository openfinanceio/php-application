<?php
namespace CFX;

trait MessageTrait {
    protected $pathIndex = 0;
    protected $r;

    public function getProtocolVersion()
    {
        return $this->r->getProtocolVersion();
    }

    public function withProtocolVersion($version)
    {
        if ($this->getProtocolVersion() === $version) {
            return $this;
        }

        $new = clone $this;
        $new->r = $new->r->withProtocolVersion($version);
        return $new;
    }

    public function getHeaders()
    {
        return $this->r->getHeaders();
    }

    public function hasHeader($header)
    {
        return $this->r->hasHeader($header);
    }

    public function getHeader($header)
    {
        return $this->r->getHeader($header);
    }

    public function getHeaderLine($header)
    {
        return $this->r->getHeaderLine($header);
    }

    public function withHeader($header, $value)
    {
        $new = clone $this;
        $new->r = $new->r->withHeader($header, $value);
        return $new;
    }

    public function withAddedHeader($header, $value)
    {
        $new = clone $this;
        $new->r = $new->r->withAddedHeader($header, $value);
        return $new;
    }

    public function withoutHeader($header)
    {
        $new = clone $this;
        $new->r = $new->r->withoutHeader($header);
        return $new;
    }

    public function getBody()
    {
        return $this->r->getBody();
    }

    public function withBody(\Psr\Http\Message\StreamInterface $body)
    {
        $new = clone $this;
        $new->r = $new->r->withBody($body);
        return $new;
    }



    /**
     * {@inheritdoc}
     */
    public function consumePathPart()
    {
        $path = explode('/', trim($this->getUri()->getPath(), '/'));
        if ($this->pathIndex >= count($path)) throw new PathOverconsumedException("You've tried to consume the next section of the path, but the entire path has already been consumed!");
        return $path[$this->pathIndex++];
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentPathPosition()
    {
        return $this->pathIndex;
    }

    /**
     * {@inheritdoc}
     */
    public function peekPathPart()
    {
        $path = explode('/', trim($this->getUri()->getPath(), '/'));
        if ($this->pathIndex >= count($path)) return null;
        return $path[$this->pathIndex];
    }




    // Factory and utility methods


    public function __clone() {
        $this->r = clone $this->r;
    }
}

