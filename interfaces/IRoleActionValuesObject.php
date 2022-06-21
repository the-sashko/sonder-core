<?php

namespace Sonder\Interfaces;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
interface IRoleActionValuesObject
{
    /**
     * @return string
     */
    public function getName(): string;
}
