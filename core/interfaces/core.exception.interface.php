<?php

namespace Sonder\Core\Interfaces;

use Attribute;
use Throwable;

#[Attribute(Attribute::TARGET_CLASS)]
interface ICoreException extends Throwable
{
    public function getHttpResponseCode(): int;
}
