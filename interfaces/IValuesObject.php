<?php

namespace Sonder\Interfaces;

use Attribute;
use JsonSerializable;

#[Attribute(Attribute::TARGET_CLASS)]
interface IValuesObject extends JsonSerializable
{
    /**
     * @return array
     */
    public function exportRow(): array;
}
