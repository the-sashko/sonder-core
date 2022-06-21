<?php

namespace Sonder\Interfaces;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
interface IEndpoint
{
    /**
     * @return void
     */
    public function run(): void;
}
