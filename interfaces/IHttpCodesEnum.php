<?php

namespace Sonder\Interfaces;

use Attribute;
use Sonder\Core\Interfaces\ICoreEnum;

#[ICoreEnum]
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
interface IHttpCodesEnum extends ICoreEnum
{
    /**
     * @return string
     */
    public function getMessage(): string;
}
