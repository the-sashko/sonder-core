<?php

namespace Sonder\Plugins\Annotation\Interfaces;

use Generator;

interface IAnnotationPlugin
{
    /**
     * @param string|null $className
     * @param string|null $methodName
     * @param string|null $annotationName
     *
     * @return string|null
     */
    public function getAnnotation(
        ?string $className = null,
        ?string $methodName = null,
        ?string $annotationName = null
    ): ?string;

    /**
     * @param string|null $className
     * @param string|null $methodName
     *
     * @return Generator
     */
    public function getMethodAnnotations(
        ?string $className = null,
        ?string $methodName = null
    ): Generator;
}
