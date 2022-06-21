<?php

namespace Sonder\Interfaces;

use Attribute;

#[IEndpoint]
#[Attribute(Attribute::TARGET_CLASS)]
interface ICliEndpoint extends IEndpoint
{
}
