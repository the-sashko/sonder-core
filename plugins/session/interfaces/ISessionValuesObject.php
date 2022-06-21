<?php

namespace Sonder\Plugins\Session\Interfaces;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
interface ISessionValuesObject
{
    /**
     * @param string $valueName
     * @return mixed
     */
    public function get(string $valueName): mixed;

    /**
     * @param string $valueName
     * @return mixed
     */
    public function getFlash(string $valueName): mixed;

    /**
     * @param string $valueName
     * @param mixed|null $valueData
     * @return void
     */
    public function set(string $valueName, mixed $valueData = null): void;

    /**
     * @param string $valueName
     * @param mixed|null $valueData
     * @return void
     */
    public function setFlash(string $valueName, mixed $valueData = null): void;

    /**
     * @param string $valueName
     * @return bool
     */
    public function remove(string $valueName): bool;

    /**
     * @param string $valueName
     * @return bool
     */
    public function has(string $valueName): bool;

    /**
     * @param string $valueName
     * @return bool
     */
    public function hasFlash(string $valueName): bool;
}
