<?php
use PHPUnit\Framework\TestCase;

/**
 * Class For Testing LanguagePlugin Class Methods
 */
class LanguagePluginTest extends TestCase
{
    public function testTranslate()
    {
        $plugin = $this->_getPlugin();

        //To-Do

        $this->assertTrue(true);
    }

    public function testGenerateDictionaries()
    {
        $plugin = $this->_getPlugin();

        //To-Do

        $this->assertTrue(true);
    }

    private function _getPlugin(): LanguagePlugin
    {
        if (empty($this->_plugin)) {
            $this->_plugin = (new CommonCore)->getPlugin('language');
        }

        return $this->_plugin;
    }
}
