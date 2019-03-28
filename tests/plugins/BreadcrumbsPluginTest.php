<?php
use PHPUnit\Framework\TestCase;

class BreadcrumbsPluginTest extends TestCase
{
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