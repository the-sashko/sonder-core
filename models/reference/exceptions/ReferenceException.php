<?php

namespace Sonder\Models\Reference\Exceptions;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;
use Sonder\Exceptions\BaseException;
use Sonder\Models\Reference\Interfaces\IReferenceException;

#[ICoreException]
#[IReferenceException]
#[Attribute(Attribute::TARGET_CLASS)]
class ReferenceException extends BaseException implements IReferenceException
{
    final public const CODE_MODEL_COLLISION = 1001;
}
