<?php

namespace Sonder\Plugins;

use Generator;
use ReflectionMethod;
use Sonder\Plugins\Annotation\Classes\AnnotationComment;
use Sonder\Plugins\Annotation\Classes\AnnotationEntity;
use Sonder\Plugins\Annotation\AnnotationException;
use Sonder\Plugins\Annotation\Interfaces\IAnnotationPlugin;

#[IAnnotationPlugin]
final class AnnotationPlugin implements IAnnotationPlugin
{
    /**
     * @param string $className
     * @param string $methodName
     * @param string $annotationName
     * @return string|null
     * @throws AnnotationException
     */
    final public function getAnnotation(
        string $className,
        string $methodName,
        string $annotationName
    ): ?string {
        if (empty($className) || empty($methodName) || empty($annotationName)) {
            return null;
        }

        $annotations = $this->getMethodAnnotations($className, $methodName);

        foreach ($annotations as $annotation) {
            if ($annotation->getName() == $annotationName) {
                return $annotation->getValue();
            }
        }

        return null;
    }

    /**
     * @param string $className
     * @param string $methodName
     * @return Generator
     * @throws AnnotationException
     */
    final public function getMethodAnnotations(
        string $className,
        string $methodName
    ): Generator {
        $comments = $this->_getMethodComments($className, $methodName);

        foreach ($comments as $comment) {
            if (!preg_match('/^@(.*?)\s(.*?)$/su', (string)$comment)) {
                continue;
            }

            [$name, $value] = preg_split('/\s+/u', $comment, 2);

            $name = preg_replace('/^@(.*?)$/', '$1', $name);

            if (empty($name)) {
                continue;
            }

            $value = !empty($value) ? $value : null;

            yield new AnnotationEntity($name, $value);
        }
    }

    /**
     * @param string $className
     * @param string $methodName
     * @return Generator
     */
    private function _getMethodComments(
        string $className,
        string $methodName
    ): Generator {
        if (empty($methodName) || !method_exists($className, $methodName)) {
            return null;
        }

        $reflection = new ReflectionMethod($className, $methodName);

        $comments = $reflection->getDocComment();
        $comments = explode("\n", $comments);

        foreach ($comments as $comment) {
            if (empty($comment)) {
                continue;
            }

            $comment = new AnnotationComment($comment);

            yield $comment->getText();
        }
    }
}
