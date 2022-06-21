<?php

namespace Sonder\Plugins\Annotation\Exceptions;

use Exception;
use Throwable;

class AnnotationException extends Exception implements Throwable
{
    const MESSAGE_NAME_IS_EMPTY = 'Annotation Name In Annotation Plugin Is Empty';

    const CODE_NAME_IS_EMPTY = 1001;
}
