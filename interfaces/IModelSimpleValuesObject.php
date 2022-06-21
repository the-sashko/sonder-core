<?php

namespace Sonder\Interfaces;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
interface IModelSimpleValuesObject
{
    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @return string|null
     */
    public function getLink(): ?string;

    /**
     * @param string|null $format
     * @return string|int|null
     */
    public function getCdate(?string $format = null): string|int|null;

    /**
     * @param string|null $format
     * @return string|int|null
     */
    public function getMdate(?string $format = null): string|int|null;

    /**
     * @param string|null $format
     * @return string|int|null
     */
    public function getDdate(?string $format = null): string|int|null;

    /**
     * @return bool
     */
    public function isActive(): bool;

    /**
     * @return bool
     */
    public function isRemoved(): bool;
}
