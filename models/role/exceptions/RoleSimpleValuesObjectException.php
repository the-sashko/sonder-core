<?php

namespace Sonder\Models\Role\Exceptions;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;
use Sonder\Models\Role\Interfaces\IRoleException;

#[ICoreException]
#[IRoleException]
#[Attribute(Attribute::TARGET_CLASS)]
final class RoleSimpleValuesObjectException extends RoleException
{
    final public const MESSAGE_ROLE_SIMPLE_VALUES_OBJECT_METHOD_NOT_IMPLEMENTED = 'Method "%s" Is Not Implemented';
}
