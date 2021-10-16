<?php

namespace Sonder\Plugins\Session\Interfaces;

interface ISessionPlugin
{
    /**
     * @param string|null $valueName
     *
     * @return mixed
     */
    public function get(?string $valueName = null): mixed;

    /**
     * @param string|null $valueName
     *
     * @return mixed
     */
    public function getFlash(?string $valueName = null): mixed;

    /**
     * @param string|null $valueName
     *
     * @param null $valueData
     */
    public function set(?string $valueName = null, $valueData = null): void;

    /**
     * @param string|null $valueName
     *
     * @param null $valueData
     */
    public function setFlash(
        ?string $valueName = null,
                $valueData = null
    ): void;

    /**
     * @param string|null $valueName
     *
     * @return bool
     */
    public function remove(?string $valueName = null): bool;

    /**
     * @param string|null $valueName
     *
     * @return bool
     */
    public function has(?string $valueName = null): bool;

    /**
     * @param string|null $valueName
     *
     * @return bool
     */
    public function hasFlash(?string $valueName = null): bool;
}
