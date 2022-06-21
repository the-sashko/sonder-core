<?php

namespace Sonder\Interfaces;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
interface IHook
{
    /**
     * @return array
     */
    public function getValues(): array;
}
