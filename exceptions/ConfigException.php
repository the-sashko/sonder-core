<?php

namespace Sonder\Exceptions;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;

#[AppException]
#[ICoreException]
#[Attribute(Attribute::TARGET_CLASS)]
final class ConfigException extends AppException
{
    final public const MESSAGE_CONFIG_INVALID_CONFIG_NAME = 'Invalid Config Name';
    final public const MESSAGE_CONFIG_VALUE_NAME_IS_NOT_SET = 'Config Value Name Is Not Set';
    final public const MESSAGE_CONFIG_NOT_EXISTS = 'Config "%s" Not Exists';
    final public const MESSAGE_CONFIG_CONFIG_IS_EMPTY = 'Config "%s" Is Empty';
    final public const MESSAGE_CONFIG_CONFIG_FILE_HAS_BAD_FORMAT = 'Config File "%s" Has Bad Name';
    final public const MESSAGE_CONFIG_CONFIG_HAS_NOT_VALUE = 'Config "%s" Has Not Value "%s"';
}