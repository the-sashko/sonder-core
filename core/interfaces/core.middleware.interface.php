<?php

namespace Sonder\Core\Interfaces;

use Attribute;
use Sonder\Interfaces\IResponseObject;

#[Attribute(Attribute::TARGET_CLASS)]
interface ICoreMiddleware
{
    public function getResponse(): ?IResponseObject;
}
