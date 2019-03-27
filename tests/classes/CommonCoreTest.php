<?php
use PHPUnit\Framework\TestCase;

class CommonCoreTest extends TestCase
{
    public function testInitPlugin()
    {
        $commonCoreTest = new CommonCore();

        $mockPlugin = $commonCoreTest->initPlugin('mock');

        $this->assertTrue($mockPlugin->mockAction());
    }
}
?>