<?php

namespace Sonder\Models\Reference\Interfaces;

use Attribute;
use Sonder\Interfaces\IValuesObject;

#[IValuesObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface IReferenceValuesObject extends IValuesObject
{
    /**
     * @return string|null
     */
    public function getReference(): ?string;

    /**
     * @param string|null $reference
     * @return void
     */
    public function setReference(?string $reference = null): void;
}
