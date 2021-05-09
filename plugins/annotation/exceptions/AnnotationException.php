<?php
namespace Core\Plugins\Annotation\Exceptions;

class AnnotationException extends \Exception
{
    const CODE_PLUGIN_CLASS_IS_EMPTY       = 1001;
    const CODE_PLUGIN_METHOD_NAME_IS_EMPTY = 1002;
    const CODE_PLUGIN_ANNOTATION_IS_EMPTY  = 1003;

    const CODE_ENTITY_NAME_IS_EMPTY        = 2001;
}
