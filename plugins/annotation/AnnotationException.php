<?php

namespace Sonder\Plugins\Annotation;

use Exception;
use Throwable;

final class AnnotationException extends Exception implements Throwable
{
    final public const MESSAGE_NAME_IS_EMPTY = 'Annotation Name In Annotation Plugin Is Empty';

    final public const CODE_NAME_IS_EMPTY = 1001;
}
