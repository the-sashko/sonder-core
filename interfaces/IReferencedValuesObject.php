<?php

namespace Sonder\Interfaces;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
interface IReferencedValuesObject
{
    /**
     * @return string|null
     */
    public function getReference(): ?string;
}
