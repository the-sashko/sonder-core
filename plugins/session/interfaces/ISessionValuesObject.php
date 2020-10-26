<?php
namespace Core\Plugins\Session\Interfaces;

interface ISessionValuesObject
{
    public function get(?string $valueName = null);

    public function getFlash(?string $valueName = null);

    public function set(?string $valueName = null, $valueData = null): void;

    public function setFlash(
        ?string $valueName = null,
                $valueData = null
    ): void;

    public function has(?string $valueName = null): bool;

    public function hasFlash(?string $valueName = null): bool;
}
