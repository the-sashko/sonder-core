<?php

namespace Sonder\Exceptions;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;

#[AppException]
#[ICoreException]
#[Attribute(Attribute::TARGET_CLASS)]
final class ModelException extends AppException
{
    final public const MESSAGE_MODEL_VALUES_OBJECT_CLASS_NOT_EXISTS = 'Values Object Class Not Exists In Model "%s"';
    final public const MESSAGE_MODEL_SIMPLE_VALUES_OBJECT_CLASS_NOT_EXISTS = 'Simple Values Object Class Not Exists In Model "%s"';
}