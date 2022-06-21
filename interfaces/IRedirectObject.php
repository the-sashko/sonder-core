<?php

namespace Sonder\Interfaces;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
interface IRedirectObject
{
    /**
     * @param string|null $url
     * @return void
     */
    public function setUrl(?string $url = null): void;

    /**
     * @param bool $isPermanent
     * @return void
     */
    public function setIsPermanent(bool $isPermanent = false): void;

    /**
     * @return void
     */
    public function redirect(): void;
}
