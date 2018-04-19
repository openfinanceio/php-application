<?php
namespace CFX;

class ConfigTest extends \PHPUnit\Framework\TestCase
{
    public function testBasicConfigWorks()
    {
        $configFile = __DIR__."/configs/test-config";
        file_put_contents("$configFile.php", "<?php return [ 'exec-profile' => 'dev', 'php-display-errors' => true, 'php-error-level' => E_ALL ];");
        $c = new Test\Config("$configFile.php", "$configFile.local.php");
        $this->assertEquals('dev', $c->getExecutionProfile());
        $this->assertTrue($c->getDisplayErrors());
        $this->assertEquals(E_ALL, $c->getErrorLevel());
    }
}

