<?php
namespace Core\Plugins\Session\Interfaces;

interface ISessionPlugin
{
    public function get(?string $valueName = null);

    public function set(?string $valueName = null, $valueData = null): void;

    public function has(?string $valueName = null): bool;
}
