<?php

namespace Sonder\Exceptions;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;

#[AppException]
#[ICoreException]
#[Attribute(Attribute::TARGET_CLASS)]
final class CacheException extends AppException
{
    final public const MESSAGE_CACHE_CAN_NOT_SAVE_VALUES = 'Can Not Save Cache Values';
}
