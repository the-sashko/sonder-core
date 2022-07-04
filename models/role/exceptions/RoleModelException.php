<?php

namespace Sonder\Models\Role\Exceptions;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;
use Sonder\Models\Role\Interfaces\IRoleException;

#[ICoreException]
#[IRoleException]
#[Attribute(Attribute::TARGET_CLASS)]
final class RoleModelException extends RoleException
{
    final public const MESSAGE_MODEL_GUEST_ROLE_NOT_EXISTS = 'Guest Role Not Exists In Database';
}
