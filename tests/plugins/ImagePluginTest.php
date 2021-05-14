<?php
use PHPUnit\Framework\TestCase;

/**
 * Class For Testing ImagePlugin Class Methods
 */
class ImagePluginTest extends TestCase
{
    const FILE_PATH_SAMPLE = __DIR__.'/../samples/images/image.jpg';

    const DIR_PATH_SAMPLE = '/tmp';

    const FILE_NAME_SAMPLE = 'image';

    const FILE_FORMAT_SAMPLE = 'png';

    const FILE_SIZES_SAMPLE = [
        [
            'small' => [
                'height'      => 64,
                'width'       => 64,
                'file_prefix' => 's'
            ]
        ],

        [
            'medium' => [
                'height'      => null,
                'width'       => 128,
                'file_prefix' => 'm'
            ]
        ]
    ];

    public function testResize()
    {
        $plugin = $this->_getPlugin();

        foreach (static::FILE_SIZES_SAMPLE as $sizes) {
            $this->assertFalse(array_key_exists(
                'phpunit_image_blob',
                $GLOBALS
            ));

            $imageBlob = $plugin->resize(
                static::FILE_PATH_SAMPLE,
                static::DIR_PATH_SAMPLE,
                static::FILE_NAME_SAMPLE,
                static::FILE_FORMAT_SAMPLE,
                $sizes
            );

            $this->assertTrue(array_key_exists(
                'phpunit_image_blob',
                $GLOBALS
            ));

            $imageBlob = $GLOBALS['phpunit_image_blob'];

            unset($GLOBALS['phpunit_image_blob']);

            $this->assertFalse(empty($imageBlob));
        }
    }

    private function _getPlugin(): ImagePlugin
    {
        if (empty($this->_plugin)) {
            $this->_plugin = (new CommonCore)->getPlugin('image');
        }

        return $this->_plugin;
    }
}
