<?php
namespace CFX\Test;

/**
 * @covers \CFX\Request
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function testRequestUriMayBeString()
    {
        $r = new \CFX\Request('GET', '/');
        $this->assertEquals('/', (string) $r->getUri());
    }

    public function testRequestUriMayBeUri()
    {
        $uri = new \GuzzleHttp\Psr7\Uri('/');
        $r = new \CFX\Request('GET', $uri);
        $this->assertSame($uri, $r->getUri());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testValidateRequestUri()
    {
        new \CFX\Request('GET', '///');
    }

    public function testCanConstructWithBody()
    {
        $r = new \CFX\Request('GET', '/', [], 'baz');
        $this->assertInstanceOf('Psr\Http\Message\StreamInterface', $r->getBody());
        $this->assertEquals('baz', (string) $r->getBody());
    }

    public function testNullBody()
    {
        $r = new \CFX\Request('GET', '/', [], null);
        $this->assertInstanceOf('Psr\Http\Message\StreamInterface', $r->getBody());
        $this->assertSame('', (string) $r->getBody());
    }

    public function testFalseyBody()
    {
        $r = new \CFX\Request('GET', '/', [], '0');
        $this->assertInstanceOf('Psr\Http\Message\StreamInterface', $r->getBody());
        $this->assertSame('0', (string) $r->getBody());
    }

    public function testConstructorDoesNotReadStreamBody()
    {
        $streamIsRead = false;
        $body = \GuzzleHttp\Psr7\FnStream::decorate(\GuzzleHttp\Psr7\stream_for(''), [
            '__toString' => function () use (&$streamIsRead) {
                $streamIsRead = true;
                return '';
            }
        ]);

        $r = new \CFX\Request('GET', '/', [], $body);
        $this->assertFalse($streamIsRead);
        $this->assertSame($body, $r->getBody());
    }

    public function testCapitalizesMethod()
    {
        $r = new \CFX\Request('get', '/');
        $this->assertEquals('GET', $r->getMethod());
    }

    public function testCapitalizesWithMethod()
    {
        $r = new \CFX\Request('GET', '/');
        $this->assertEquals('PUT', $r->withMethod('put')->getMethod());
    }

    public function testWithUri()
    {
        $r1 = new \CFX\Request('GET', '/');
        $u1 = $r1->getUri();
        $u2 = new \GuzzleHttp\Psr7\Uri('http://www.example.com');
        $r2 = $r1->withUri($u2);
        $this->assertNotSame($r1, $r2);
        $this->assertSame($u2, $r2->getUri());
        $this->assertSame($u1, $r1->getUri());
    }

    public function testSameInstanceWhenSameUri()
    {
        $r1 = new \CFX\Request('GET', 'http://foo.com');
        $r2 = $r1->withUri($r1->getUri());
        $this->assertSame($r1, $r2);
    }

    public function testWithRequestTarget()
    {
        $r1 = new \CFX\Request('GET', '/');
        $r2 = $r1->withRequestTarget('*');
        $this->assertEquals('*', $r2->getRequestTarget());
        $this->assertEquals('/', $r1->getRequestTarget());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRequestTargetDoesNotAllowSpaces()
    {
        $r1 = new \CFX\Request('GET', '/');
        $r1->withRequestTarget('/foo bar');
    }

    public function testRequestTargetDefaultsToSlash()
    {
        $r1 = new \CFX\Request('GET', '');
        $this->assertEquals('/', $r1->getRequestTarget());
        $r2 = new \CFX\Request('GET', '*');
        $this->assertEquals('*', $r2->getRequestTarget());
        $r3 = new \CFX\Request('GET', 'http://foo.com/bar baz/');
        $this->assertEquals('/bar%20baz/', $r3->getRequestTarget());
    }

    public function testBuildsRequestTarget()
    {
        $r1 = new \CFX\Request('GET', 'http://foo.com/baz?bar=bam');
        $this->assertEquals('/baz?bar=bam', $r1->getRequestTarget());
    }

    public function testBuildsRequestTargetWithFalseyQuery()
    {
        $r1 = new \CFX\Request('GET', 'http://foo.com/baz?0');
        $this->assertEquals('/baz?0', $r1->getRequestTarget());
    }

    public function testHostIsAddedFirst()
    {
        $r = new \CFX\Request('GET', 'http://foo.com/baz?bar=bam', ['Foo' => 'Bar']);
        $this->assertEquals([
            'Host' => ['foo.com'],
            'Foo'  => ['Bar']
        ], $r->getHeaders());
    }

    public function testCanGetHeaderAsCsv()
    {
        $r = new \CFX\Request('GET', 'http://foo.com/baz?bar=bam', [
            'Foo' => ['a', 'b', 'c']
        ]);
        $this->assertEquals('a, b, c', $r->getHeaderLine('Foo'));
        $this->assertEquals('', $r->getHeaderLine('Bar'));
    }

    public function testHostIsNotOverwrittenWhenPreservingHost()
    {
        $r = new \CFX\Request('GET', 'http://foo.com/baz?bar=bam', ['Host' => 'a.com']);
        $this->assertEquals(['Host' => ['a.com']], $r->getHeaders());
        $r2 = $r->withUri(new \GuzzleHttp\Psr7\Uri('http://www.foo.com/bar'), true);
        $this->assertEquals('a.com', $r2->getHeaderLine('Host'));
    }

    public function testOverridesHostWithUri()
    {
        $r = new \CFX\Request('GET', 'http://foo.com/baz?bar=bam');
        $this->assertEquals(['Host' => ['foo.com']], $r->getHeaders());
        $r2 = $r->withUri(new \GuzzleHttp\Psr7\Uri('http://www.baz.com/bar'));
        $this->assertEquals('www.baz.com', $r2->getHeaderLine('Host'));
    }

    public function testAggregatesHeaders()
    {
        $r = new \CFX\Request('GET', '', [
            'ZOO' => 'zoobar',
            'zoo' => ['foobar', 'zoobar']
        ]);
        $this->assertEquals(['ZOO' => ['zoobar', 'foobar', 'zoobar']], $r->getHeaders());
        $this->assertEquals('zoobar, foobar, zoobar', $r->getHeaderLine('zoo'));
    }

    public function testAddsPortToHeader()
    {
        $r = new \CFX\Request('GET', 'http://foo.com:8124/bar');
        $this->assertEquals('foo.com:8124', $r->getHeaderLine('host'));
    }

    public function testAddsPortToHeaderAndReplacePreviousPort()
    {
        $r = new \CFX\Request('GET', 'http://foo.com:8125/bar');
        $r = $r->withUri(new \GuzzleHttp\Psr7\Uri('http://foo.com:8125/bar'));
        $this->assertEquals('foo.com:8125', $r->getHeaderLine('host'));
    }










    // CFX Tests

    public function testCanConsumePathParts()
    {
        $r = new \CFX\Request('GET', "http://foo.com:8125/bar/baz/bizzle");

        $part = $r->consumePathPart();
        $this->assertEquals("bar", $part);

        $part = $r->consumePathPart();
        $this->assertEquals("baz", $part);

        $part = $r->consumePathPart();
        $this->assertEquals("bizzle", $part);

        try {
            $part = $r->consumePathPart();
            $this->fail("Should have thrown an exception");
        } catch(\CFX\PathOverconsumedException $e) {
            $this->assertTrue(true, "This is the expected behavior");
        }
    }

    public function testPathConsumptionResetsOnUriChange()
    {
        $r = new \CFX\Request('GET', "http://foo.com:8125/bar/baz/bizzle");

        $part = $r->consumePathPart();
        $this->assertEquals("bar", $part);

        $part = $r->consumePathPart();
        $this->assertEquals("baz", $part);

        $r = $r->withUri(new \GuzzleHttp\Psr7\Uri('http://foo.com:8125/bar/baz/sizzle'));

        $part = $r->consumePathPart();
        $this->assertEquals("bar", $part);

        $part = $r->consumePathPart();
        $this->assertEquals("baz", $part);

        $part = $r->consumePathPart();
        $this->assertEquals("sizzle", $part);
    }

    public function testPathConsumptionDoesntResetWhenNewUriIsTheSame()
    {
        $r = new \CFX\Request('GET', "http://foo.com:8125/bar/baz/bizzle");

        $part = $r->consumePathPart();
        $this->assertEquals("bar", $part);

        $part = $r->consumePathPart();
        $this->assertEquals("baz", $part);

        $r = $r->withUri(new \GuzzleHttp\Psr7\Uri('http://foo.com:8125/bar/baz/bizzle'));

        $part = $r->consumePathPart();
        $this->assertEquals("bizzle", $part);
    }
}
