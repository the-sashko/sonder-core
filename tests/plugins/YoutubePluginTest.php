<?php
use PHPUnit\Framework\TestCase;

/**
 * Class For Testing YoutubePlugin Class Methods
 */
class YoutubePluginTest extends TestCase
{
    /**
     * @var array Data Sample For Unit Tests
     */
    const TEXT_DATA_SAMPLE = [
        [
            'value'    => '',
            'expected' => ''
        ],
        [
            'value'    => 'Lorem ipsum dolor sit amet',
            'expected' => 'Lorem ipsum dolor sit amet'
        ],
        [
            'value'    => 'Lorem ipsum dolor https://www.youtube.com/watch?'.
                          'v=UnitTest1 sit amet, consectetur http://m.youtu.be'.
                          '/UnitTest3?t=333 adipiscing elit. Nullam finibus '.
                          'semper diam at dapibus. Donec https://www.youtube'.
                          '.com/watch?v=UnitTest2&t=111 at convallis dui. '.
                          'Proin et velit enim.',
            'expected' => 'Lorem ipsum dolor [Youtube:UnitTest1] sit amet, '.
                          'consectetur [Youtube:UnitTest3?t=333s] adipiscing '.
                          'elit. Nullam finibus semper diam at dapibus. Donec '.
                          '[Youtube:UnitTest2?t=111s] at convallis dui. Proin '.
                          'et velit enim.'
        ]
    ];

    /**
     * Unit Test Of parseYoutubeURL Method
     */
    public function testParseYoutubeURL()
    {
        $youtube = (new CommonCore)->initPlugin('youtube');

        foreach (static::TEXT_DATA_SAMPLE as $text) {
            $res = $youtube->parseYoutubeURL($text['value']);
            $this->assertEquals($text['expected'], $res);
        }
    }
}
?>
