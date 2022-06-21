<?php

namespace Sonder\Interfaces;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
interface IReferencedValuesObject
{
    /**
     * @return int|string|null
     */
    public function getReference(): int|string|null;
}
