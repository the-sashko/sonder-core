<?php

namespace Sonder\Models\Reference\Exceptions;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;
use Sonder\Models\Reference\Interfaces\IReferenceException;

#[ICoreException]
#[IReferenceException]
#[Attribute(Attribute::TARGET_CLASS)]
final class ReferenceModelException extends ReferenceException
{
    final public const MESSAGE_MODEL_COLLISION = 'Can not create UUID reference due collision';
}
