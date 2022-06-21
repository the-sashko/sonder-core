<?php

namespace Sonder\Core\Interfaces;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
interface IAutoloadCore
{
    public function load(?string $className = null): bool;
}
