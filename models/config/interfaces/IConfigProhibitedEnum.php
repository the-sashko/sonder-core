<?php

namespace Sonder\Models\Config\Interfaces;

use Attribute;
use Sonder\Core\Interfaces\ICoreEnum;

#[ICoreEnum]
#[Attribute(Attribute::TARGET_CLASS)]
interface IConfigProhibitedEnum extends ICoreEnum
{
}
