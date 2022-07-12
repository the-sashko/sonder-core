<?php

namespace Sonder\Models\Reference\Interfaces;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;

#[ICoreException]
#[Attribute(Attribute::TARGET_CLASS)]
interface IReferenceException extends ICoreException
{
}
