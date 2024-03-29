<?php

namespace Sonder\Exceptions;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;

#[AppException]
#[ICoreException]
#[Attribute(Attribute::TARGET_CLASS)]
final class HookException extends AppException
{
    final public const MESSAGE_HOOK_VALUE_NOT_EXISTS = 'Value "%s" Not Exists In Hook Values';
}
