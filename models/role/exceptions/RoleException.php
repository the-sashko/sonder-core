<?php

namespace Sonder\Models\Role\Exceptions;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;
use Sonder\Exceptions\BaseException;
use Sonder\Models\Role\Interfaces\IRoleException;

#[ICoreException]
#[IRoleException]
#[Attribute(Attribute::TARGET_CLASS)]
class RoleException extends BaseException implements IRoleException
{
    final public const CODE_ROLE_SIMPLE_VALUES_OBJECT_METHOD_NOT_IMPLEMENTED = 1001;

    final public const CODE_MODEL_GUEST_ROLE_NOT_EXISTS = 2001;
}
