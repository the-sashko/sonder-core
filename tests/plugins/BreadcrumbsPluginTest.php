<?php
use PHPUnit\Framework\TestCase;

/**
 * Class For Testing BreadcrumbsPlugin Class Methods
 */
class BreadcrumbsPluginTest extends TestCase
{
    /**
     * @var array Data Sample For Unit Tests
     */
    const PATH_DATA_SAMPLE = [
        [
            'value'     => [],
            'expected'  => '<ul class="breadcrumbs">'."\n".'    <li>'."\n".
                           '    <span>Main Page</span>'."\n".'</li>'."\n".
                           '</ul>'
        ],
        [
            'value'     => [
                '/foo/' => 'foo',
                '/bar/' => 'bar',
                '#'     => 'current'
            ],
            'expected'  => '<ul class="breadcrumbs">'."\n".'    <li>'."\n".
                           '    <a href="/">Main Page</a>'."\n".'</li><li>'.
                           "\n".'    <a href="/foo/">foo</a>'."\n".'</li><li>'.
                           "\n".'    <a href="/bar/">bar</a>'."\n".'</li><li>'.
                           "\n".'    <span>current</span>'."\n".'</li>'."\n".
                           '</ul>'
        ]
    ];

    /**
     * Unit Test Of getHTML Method
     */
    public function testGetHTML()
    {
        $breadcrumbs = (new CommonCore)->getPlugin('breadcrumbs');

        foreach (static::PATH_DATA_SAMPLE as $path) {
            $html = $breadcrumbs->getHTML($path['value']);
            $this->assertEquals($path['expected'], $html);
        }
    }
}
?>