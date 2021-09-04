<?php

namespace Core\Tests\Samples;

class ClassWithAnnotations
{
    final public function foo(): bool
    {
        return true;
    }

    /**
     * List Of Annotations
     *
     * @one First Annotation
     * @two Second Annotation
     *
     * Line Without annotations
     * @three Third Annotation
     * @thisIsNotAnnotation
     * @badformatted           Bad Formatted  Annotation 123
     */
    final public function bar(): bool
    {
        return true;
    }

    /**
     * @first  111
     * @second 222
     * @third  333
     */
    final public function test(): bool
    {
        return true;
    }
}
