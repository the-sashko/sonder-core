<?php

namespace Sonder\Interfaces;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
interface IModelFormFileObject
{
    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @return string|null
     */
    public function getExtension(): ?string;

    /**
     * @return int|null
     */
    public function getSize(): ?int;

    /**
     * @return string|null
     */
    public function getPath(): ?string;

    /**
     * @return bool
     */
    public function getError(): bool;
}
