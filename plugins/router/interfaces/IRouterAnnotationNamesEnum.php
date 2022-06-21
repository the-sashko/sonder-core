<?php

namespace Sonder\Plugins\Router\Interfaces;

use Attribute;
use BackedEnum;

#[Attribute(Attribute::TARGET_CLASS)]
interface IRouterAnnotationNamesEnum extends BackedEnum
{
}
