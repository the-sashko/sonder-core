<?php

namespace Sonder\Plugins\Annotation\Interfaces;

use Attribute;
use Generator;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
interface IAnnotationPlugin
{
    /**
     * @param string $className
     * @param string $methodName
     * @param string $annotationName
     * @return string|null
     */
    public function getAnnotation(
        string $className,
        string $methodName,
        string $annotationName
    ): ?string;

    /**
     * @param string $className
     * @param string $methodName
     * @return Generator
     */
    public function getMethodAnnotations(
        string $className,
        string $methodName
    ): Generator;
}
