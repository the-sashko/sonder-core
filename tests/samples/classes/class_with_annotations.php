<?php
namespace Core\Tests\Samples;

class ClassWithAnnotations
{
    public function foo(): bool
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
*        @badformated           Bad Formated  Ann0tation
     */
    public function bar(): bool
    {
        return true;
    }

    /**
     * @first  111
     * @second 222
     * @third  333
     */
    public function test(): bool
    {
        return true;
    }
}
