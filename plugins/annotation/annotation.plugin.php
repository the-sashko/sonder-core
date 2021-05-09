<?php
use Core\Plugins\Annotation\Interfaces\IAnnotationPlugin;

use Core\Plugins\Annotation\Classes\AnnotationComment;
use Core\Plugins\Annotation\Classes\AnnotationEntity;

use Core\Plugins\Annotation\Exceptions\AnnotationPluginException;

class AnnotationPlugin implements IAnnotationPlugin
{
    public function getAnnotation(
        ?string $className      = null,
        ?string $methodName     = null,
        ?string $annotationName = null
    ): ?string
    {
        if (empty($className)) {
            throw new AnnotationPluginException(
                AnnotationPluginException::MESSAGE_PLUGIN_CLASS_IS_EMPTY,
                AnnotationPluginException::CODE_PLUGIN_CLASS_IS_EMPTY
            );
        }

        if (empty($methodName)) {
            throw new AnnotationPluginException(
                AnnotationPluginException::MESSAGE_PLUGIN_METHOD_NAME_IS_EMPTY,
                AnnotationPluginException::CODE_PLUGIN_METHOD_NAME_IS_EMPTY
            );
        }

        if (empty($annotationName)) {
            throw new AnnotationPluginException(
                AnnotationPluginException::MESSAGE_PLUGIN_ANNOTATION_IS_EMPTY,
                AnnotationPluginException::CODE_PLUGIN_ANNOTATION_IS_EMPTY
            );
        }

        $annotaions = $this->getMethodAnnotations($className, $methodName);

        foreach ($annotaions as $annotation) {
            if ($annotation->getName() == $annotationName) {
                return $annotation->getValue();
            }
        }

        return null;
    }

    public function getMethodAnnotations(
        ?string $className  = null,
        ?string $methodName = null
    ): \Generator
    {
        if (empty($className)) {
            throw new AnnotationPluginException(
                AnnotationPluginException::MESSAGE_PLUGIN_CLASS_IS_EMPTY,
                AnnotationPluginException::CODE_PLUGIN_CLASS_IS_EMPTY
            );
        }

        if (empty($methodName)) {
            throw new AnnotationPluginException(
                AnnotationPluginException::MESSAGE_PLUGIN_METHOD_NAME_IS_EMPTY,
                AnnotationPluginException::CODE_PLUGIN_METHOD_NAME_IS_EMPTY
            );
        }

        $comments = $this->_getMethodComments($className, $methodName);

        foreach ($comments as $comment) {
            if (!preg_match('/^@(.*?)\s(.*?)$/su', $comment)) {
                continue;
            }

            list($name, $value) = preg_split('/\s+/su', $comment, 2);

            $name = preg_replace('/^@(.*?)$/', '$1', $name);

            if (empty($name)) {
                continue;
            }

            $value = !empty($value) ? $value : null;

            yield new AnnotationEntity($name, $value);
        }
    }

    private function _getMethodComments(
        string $className,
        string $methodName
    ): ?\Generator
    {
        if (empty($methodName) || !method_exists($className, $methodName)) {
            return null;
        }

        $reflection = new ReflectionMethod($className, $methodName);

        $comments = $reflection->getDocComment();
        $comments = explode("\n", $comments);

        foreach ($comments as $comment) {
            if (!empty($comment)) {
                $comment = new AnnotationComment($comment);

                yield $comment->getComment();
            }
        }
    }
}
