<?php

namespace Sonder\Interfaces;

use Attribute;

#[IMiddleware]
#[Attribute(Attribute::TARGET_CLASS)]
interface IUserMiddleware extends IMiddleware
{
}
