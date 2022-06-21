<?php

namespace Sonder\Core\Interfaces;

use Attribute;
use BackedEnum;

#[Attribute(Attribute::TARGET_CLASS)]
interface ICoreEnum extends BackedEnum
{
}
