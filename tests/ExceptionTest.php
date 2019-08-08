<?php
namespace CFX;

class ExceptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider exceptionProvider
     */
    public function testExceptions($e, string $baseclass)
    {
        $o = [
            "code" => "ObOne",
            "text" => "Obstruction One",
            "params" => [
                "country" => "US"
            ]
        ];
        $e->setObstructions([$o]);

        try {
            throw $e;
        } catch (\Exception $e) {
            $ob = $e->getObstructions();
            $this->assertEquals(count($ob), 1);
            $this->assertEquals($ob[0]["code"], $o["code"]);
            $this->assertEquals($ob[0]["text"], $o["text"]);
            $this->assertEquals($ob[0]["params"], $o["params"]);

            $ob = $e->getJsonApiObstructions();
            $this->assertEquals(count($ob), 1);
            $this->assertEquals($ob[0]["id"], $o["code"]);
            $this->assertEquals($ob[0]["type"], "obstructions");
            $this->assertEquals($ob[0]["attributes"]["detail"], $o["text"]);
            $this->assertEquals($ob[0]["attributes"]["params"], $o["params"]);

            $this->assertInstanceOf($baseclass, $e);
        }
    }

    public function exceptionProvider()
    {
        return [
            [ new Exception("Test Exception"), "\RuntimeException" ],
            [ new DebugException("Test Exception"), "\RuntimeException" ],
            [ new UnimplementedFeatureException("Test Exception"), "\RuntimeException" ],
            [ new ResourceNotFoundException("Test Exception"), "\InvalidArgumentException" ],
            [ new BadInputException("Test Exception"), "\InvalidArgumentException" ],
        ];
    }
}
