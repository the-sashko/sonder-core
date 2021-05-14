<?php
use PHPUnit\Framework\TestCase;

/**
 * Class For Testing AnnotationPlugin Class Methods
 */
class AnnotationPluginTest extends TestCase
{
    const CLASS_SAMPLE = 'Core\Tests\Samples\ClassWithAnnotations';

    const ANNOTATIONS_SAMPLE = [
        'foo' => null,

        'bar' => [
            [
                'name'  => 'one',
                'value' => 'First Annotation'
            ],
            [
                'name'  => 'two',
                'value' => 'Second Annotation'
            ],
            [
                'name'  => 'three',
                'value' => 'Third Annotation'
            ],
            [
                'name'  => 'badformated',
                'value' => 'Bad Formated Ann0tation'
            ]
        ],

        'test' => [
            [
                'name'  => 'first',
                'value' => '111'
            ],
            [
                'name'  => 'second',
                'value' => '222'
            ],
            [
                'name'  => 'third',
                'value' => '333'
            ]
        ]
    ];

    private $_plugin = null;

    public function testGetAnnotation()
    {
        $this->assertTrue(true);
    }

    public function testGetMethodAnnotations()
    {
        $this->_plugin = $this->_getPlugin();

        foreach (static::ANNOTATIONS_SAMPLE as $method => $sampleAnnotations) {
            $annotations = $this->_getMethodAnnotations($method);

            $this->assertEquals($annotations, $sampleAnnotations);
        }
    }

    public function testGetMethodAnnotation()
    {
        $this->_plugin = $this->_getPlugin();

        foreach (static::ANNOTATIONS_SAMPLE as $method => $sampleAnnotations) {
            if (empty($sampleAnnotations)) {
                continue;
            }

            $this->assertTrue($this->_testGetMethodSingleAnnotations(
                $method,
                $sampleAnnotations
            ));
        }
    }

    private function _testGetMethodSingleAnnotations(
        string $method,
        array  $sampleAnnotations
    ): bool
    {
        foreach ($sampleAnnotations as $sampleAnnotation) {
            $annotation = $this->_plugin->getAnnotation(
                static::CLASS_SAMPLE,
                $method,
                $sampleAnnotation['name']
            );

            $this->assertEquals($annotation, $sampleAnnotation['value']);
        }

        return true;
    }

    private function _getMethodAnnotations(string $method): ?array
    {
        $annotations = $this->_plugin->getMethodAnnotations(
            static::CLASS_SAMPLE,
            $method
        );

        $annotations = iterator_to_array($annotations);

        if (empty($annotations)) {
            return null;
        }

        foreach ($annotations as $key => $annotation) {
            $annotations[$key] = [
                'name'  => $annotation->getName(),
                'value' => $annotation->getValue()
            ];
        }

        return $annotations;
    }

    private function _getPlugin(): AnnotationPlugin
    {
        if (empty($this->_plugin)) {
            $this->_plugin = (new CommonCore)->getPlugin('annotation');
        }

        return $this->_plugin;
    }
}
