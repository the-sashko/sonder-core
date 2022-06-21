<?php

namespace Sonder\Interfaces;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
interface ICacheObject
{
    /**
     * @param string $name
     * @return array|null
     */
    public function get(string $name): ?array;

    /**
     * @param string $name
     * @param array|null $values
     * @param int|null $ttl
     * @return void
     */
    public function save(
        string $name,
        ?array $values = null,
        ?int $ttl = null
    ): void;

    /**
     * @param string $name
     * @return void
     */
    public function remove(string $name): void;

    /**
     * @param string|null $type
     * @return void
     */
    public function removeAll(?string $type = null): void;
}
