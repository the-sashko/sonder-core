<?php
use PHPUnit\Framework\TestCase;

/**
 * Class For Testing CommonCore Class Methods
 */
class CommonCoreTest extends TestCase
{
   /**
    * Unit Test Of InitPlugin Method
    */
    public function testInitPlugin()
    {
        $commonCoreTest = new CommonCore();

        $mockPlugin = $commonCoreTest->initPlugin('mock');

        $this->assertTrue($mockPlugin->mockAction());
    }
}
?>