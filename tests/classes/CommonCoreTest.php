<?php

use PHPUnit\Framework\TestCase;

/**
 * Class For Testing CommonCore Class Methods
 */
class CommonCoreTest extends TestCase
{
    /**
     * Unit Test Of InitPlugin Method
     *
     * @throws CoreException
     */
    final public function testInitPlugin()
    {
        $commonCoreTest = new CommonCore();

        $mockPlugin = $commonCoreTest->getPlugin('mock');

        $this->assertTrue($mockPlugin->mockAction());
    }
}
