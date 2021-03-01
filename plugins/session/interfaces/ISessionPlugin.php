<?php
namespace Core\Plugins\Session\Interfaces;

interface ISessionPlugin
{
    public function get(?string $valueName = null);

    public function getFlash(?string $valueName = null);

    public function set(?string $valueName = null, $valueData = null): void;

    public function setFlash(
        ?string $valueName = null,
                $valueData = null
    ): void;

    public function remove(?string $valueName = null): bool;

    public function has(?string $valueName = null): bool;

    public function hasFlash(?string $valueName = null): bool;
}
