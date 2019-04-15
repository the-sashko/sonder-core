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
            'expected'  => '<nav class="breadcrumbs">'.
                           '<span>Main Page</span></nav>'
        ],
        [
            'value'     => [
                '/foo/' => 'foo',
                '/bar/' => 'bar',
                '#'     => 'current'
            ],
            'expected'  => '<nav class="breadcrumbs">'.
                           '<a href="/">Main Page</a><span>»</span>'.
                           '<a href="/foo/">foo</a><span>»</span>'.
                           '<a href="/bar/">bar</a><span>»</span>'.
                           '<span>current</span></nav>'
        ]
    ];

    /**
     * Unit Test Of getHTML Method
     */
    public function testGetHTML()
    {
        $breadcrumbs = (new CommonCore)->initPlugin('breadcrumbs');

        foreach (static::PATH_DATA_SAMPLE as $path) {
            $html = $breadcrumbs->getHTML($path['value']);
            $this->assertEquals($path['expected'], $html);
        }
    }
}
?>