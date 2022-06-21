<?php

namespace Sonder\Exceptions;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;

#[AppException]
#[ICoreException]
#[Attribute(Attribute::TARGET_CLASS)]
final class CoreException extends AppException
{
    final public const MESSAGE_CORE_MODEL_NOT_EXISTS = 'Model "%s" Not Exists';
    final public const MESSAGE_CORE_PLUGIN_NOT_EXISTS = 'Plugin "%s" Not Exists';
}
