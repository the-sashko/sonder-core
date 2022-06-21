<?php

namespace Sonder\Exceptions;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;

#[AppException]
#[ICoreException]
#[Attribute(Attribute::TARGET_CLASS)]
final class ValuesObjectException extends AppException
{
    final public const MESSAGE_VALUES_OBJECT_EMPTY_VALUE_NAME = 'Can Not Set Value With Empty Name To "%s"';
    final public const MESSAGE_VALUES_OBJECT_VALUE_NOT_FOUND = 'Value "%s" Not Found In %s';
}
