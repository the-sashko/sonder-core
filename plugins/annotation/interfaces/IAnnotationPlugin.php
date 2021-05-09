<?php
namespace Core\Plugins\Annotation\Interfaces;

interface IAnnotationPlugin
{
    public function getAnnotation(
        ?string $className      = null,
        ?string $methodName     = null,
        ?string $annotationName = null
    ): ?string;

    public function getMethodAnnotations(
        ?string $className  = null,
        ?string $methodName = null
    ): \Generator;
}
