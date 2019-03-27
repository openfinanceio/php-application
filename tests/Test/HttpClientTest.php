<?php
namespace CFX\Test;

class HttpClientTest extends \PHPUnit\Framework\TestCase
{
    protected $httpClient;

    public function setUp()
    {
        $this->httpClient = new HttpClient();
    }

    public function testSetResponse()
    {
        $r = new \GuzzleHttp\Psr7\Response();
        $this->httpClient->setNextResponse($r);
        $this->assertSame($r, $this->httpClient->send(new \GuzzleHttp\Psr7\Request("GET", "/test")));
    }

    public function testThrowsExceptionIfNoResponseSet()
    {
        try {
            $this->httpClient->send(new \GuzzleHttp\Psr7\Request("GET", "/test"));
            $this->fail("Should have thrown an exception");
        } catch(\RuntimeException $e) {
            $this->assertEquals(
                "This is a test HTTP Client that does not make real HTTP calls. You must set the response ".
                "for the request you're about to execute by using the `setNextResponse(\GuzzleHttp\Message".
                "\ResponseInterface \$r)` method.",
                $e->getMessage()
            );
        }
    }

    public function testThrowsExceptionIfGivenAnExceptionAsNextResponse()
    {
        $req = new \GuzzleHttp\Psr7\Request("GET", "/test");
        $res = new \GuzzleHttp\Exception\ServerException("Something happened", $req);
        $this->httpClient->setNextResponse($res);
        try {
            $this->httpClient->send($req);
            $this->fail("Should have thrown an exception");
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            $this->assertSame($res, $e);
        }
    }

    public function testFIFO()
    {
        $r1 = new \GuzzleHttp\Psr7\Response();
        $r2 = new \GuzzleHttp\Psr7\Response();
        $this->httpClient
            ->setNextResponse($r1)
            ->setNextResponse($r2);

        $this->assertSame($r1, $this->httpClient->send(new \GuzzleHttp\Psr7\Request("GET", "/test")));
        $this->assertSame($r2, $this->httpClient->send(new \GuzzleHttp\Psr7\Request("GET", "/test")));
    }

    /**
     * @dataProvider httpMethodsProvider
     */
    public function testAllHttpMethodsAreStubbedOut($method)
    {
        $r = new \GuzzleHttp\Psr7\Response();
        $this->httpClient->setNextResponse($r);
        $this->assertSame($r, $this->httpClient->$method("/test"));
    }

    public function httpMethodsProvider()
    {
        return [
            [ "get" ],
            [ "post" ],
            [ "patch" ],
            [ "put" ],
            [ "delete" ],
        ];
    }
}
