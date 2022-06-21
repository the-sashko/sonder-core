<?php

namespace Sonder\Interfaces;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
interface IModelValuesObject
{
    /**
     * @return string|null
     */
    public function getEditLink(): ?string;

    /**
     * @return string|null
     */
    public function getRemoveLink(): ?string;

    /**
     * @return string|null
     */
    public function getRestoreLink(): ?string;

    /**
     * @param string|null $reference
     * @return void
     */
    public function setReference(?string $reference = null): void;

    /**
     * @param bool $isActive
     * @return void
     */
    public function setIsActive(bool $isActive = true): void;

    /**
     * @return void
     */
    public function setCdate(): void;

    /**
     * @return void
     */
    public function setMdate(): void;

    /**
     * @return void
     */
    public function setDdate(): void;
}
