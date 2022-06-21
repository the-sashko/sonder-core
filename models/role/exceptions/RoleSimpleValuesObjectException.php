<?php

namespace Sonder\Models\Role\Exceptions;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;

#[RoleException]
#[ICoreException]
#[Attribute(Attribute::TARGET_CLASS)]
final class RoleSimpleValuesObjectException extends RoleException
{
    final public const MESSAGE_ROLE_SIMPLE_VALUES_OBJECT_METHOD_NOT_IMPLEMENTED = 'Method "%s" Is Not Implemented';
}
