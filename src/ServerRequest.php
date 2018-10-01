<?php
namespace CFX;

/**
 * Adapts Guzzle's PSR7 ServerRequest implementation. All documentation is taken from https://github.com/guzzle/psr7/blob/master/src/ServerRequest.php
 */
class ServerRequest extends Request implements ServerRequestInterface
{
    protected $apiKey = null;

    /**
     * @param string                               $method       HTTP method
     * @param string|UriInterface                  $uri          URI
     * @param array                                $headers      Request headers
     * @param string|null|resource|StreamInterface $body         Request body
     * @param string                               $version      Protocol version
     * @param array                                $serverParams Typically the $_SERVER superglobal
     */
    public function __construct(
        $method,
        $uri,
        array $headers = [],
        $body = null,
        $version = '1.1',
        array $serverParams = []
    ) {
        $r = static::getRequestClass();
        $this->r = new $r($method, $uri, $headers, $body, $version, $serverParams);
    }

    /**
     * Return an UploadedFile instance array.
     *
     * @param array $files A array which respect $_FILES structure
     * @throws InvalidArgumentException for unrecognized values
     * @return array
     */
    public static function normalizeFiles(array $files)
    {
        $r = static::getRequestClass();
        return $r::normalizeFiles($files);
    }

    /**
     * Return a ServerRequest populated with superglobals:
     * $_GET
     * $_POST
     * $_COOKIE
     * $_FILES
     * $_SERVER
     *
     * @return ServerRequestInterface
     */
    public static function fromGlobals()
    {
        $request = static::getRequestClass();
        $r = new static('GET', '/');
        $r->r = $request::fromGlobals();
        return $r;
    }

    /**
     * Get a Uri populated with values from $_SERVER.
     *
     * @return UriInterface
     */
    public static function getUriFromGlobals() {
        $r = static::getRequestClass();
        return $r::getUriFromGlobals();
    }

    /**
     * {@inheritdoc}
     */
    public function getServerParams()
    {
        return $this->r->getServerParams();
    }

    /**
     * {@inheritdoc}
     */
    public function getUploadedFiles()
    {
        return $this->r->getUploadedFiles();
    }

    /**
     * {@inheritdoc}
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        $newR = $this->r->withUploadedFiles($uploadedFiles);
        if ($newR === $this->r) return $this;

        $new = clone $this;
        $new->r = $newR;
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function getCookieParams()
    {
        return $this->r->getCookieParams();
    }

    /**
     * {@inheritdoc}
     */
    public function withCookieParams(array $cookies)
    {
        $newR = $this->r->withCookieParams($cookies);
        if ($newR === $this->r) return $this;

        $new = clone $this;
        $new->r = $newR;
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryParams()
    {
        return $this->r->getQueryParams();
    }

    /**
     * {@inheritdoc}
     */
    public function withQueryParams(array $query)
    {
        $newR = $this->r->withQueryParams($query);
        if ($newR === $this->r) return $this;

        $new = clone $this;
        $new->r = $newR;
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function getParsedBody()
    {
        return $this->r->getParsedBody();
    }

    /**
     * {@inheritdoc}
     */
    public function withParsedBody($data)
    {
        $newR = $this->r->withParsedBody($data);
        if ($newR === $this->r) return $this;

        $new = clone $this;
        $new->r = $newR;
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return $this->r->getAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($attribute, $default = null)
    {
        return $this->r->getAttribute($attribute, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function withAttribute($attribute, $value)
    {
        $newR = $this->r->withAttribute($attribute, $value);
        if ($newR === $this->r) return $this;

        $new = clone $this;
        $new->r = $newR;
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutAttribute($attribute)
    {
        $newR = $this->r->withoutAttribute($attribute);
        if ($newR === $this->r) return $this;

        $new = clone $this;
        $new->r = $newR;
        return $new;
    }



    /**
     * getRequestClass -- Get the fully-qualified class name of the underlying class that provides functionality for this class.
     *
     * This is a sort of factory method, as this class name is used both to instantiate as well as to access certain static
     * functions.
     *
     * @return string The fully-qualified class name of the psr7-compatible implementing class
     */
    protected static function getRequestClass()
    {
        return "\\GuzzleHttp\\Psr7\\ServerRequest";
    }







    // CFX-Specific functions

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function setApiKey($val)
    {
        $this->apiKey = $val;
    }
}

