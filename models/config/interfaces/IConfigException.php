<?php

namespace Sonder\Models\Config\Interfaces;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;

#[ICoreException]
#[Attribute(Attribute::TARGET_CLASS)]
interface IConfigException extends ICoreException
{
}
