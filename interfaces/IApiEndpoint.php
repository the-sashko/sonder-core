<?php

namespace Sonder\Interfaces;

use Attribute;

#[IEndpoint]
#[Attribute(Attribute::TARGET_CLASS)]
interface IApiEndpoint extends IEndpoint
{
}
