<?php

namespace Sonder\Interfaces;

use Attribute;
use Sonder\Core\Interfaces\ICoreMiddleware;

#[ICoreMiddleware]
#[Attribute(Attribute::TARGET_CLASS)]
interface IMiddleware extends ICoreMiddleware
{
    /**
     * @return void
     */
    public function run(): void;
}
