<?php

namespace Sonder\Models\Role\Interfaces;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;

#[ICoreException]
#[Attribute(Attribute::TARGET_CLASS)]
interface IRoleException extends ICoreException
{
}
