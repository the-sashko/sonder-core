<?php

namespace Sonder\Models\Role\Exceptions;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;

#[RoleException]
#[ICoreException]
#[Attribute(Attribute::TARGET_CLASS)]
final class RoleModelObjectException extends RoleException
{
    final public const MESSAGE_MODEL_GUEST_ROLE_NOT_EXISTS = 'Guest Role Not Exists In Database';
}
