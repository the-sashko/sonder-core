<?php
namespace Core\Plugins\Session\Interfaces;

interface ISessionValuesObject
{
    public function get(?string $valueName = null);

    public function set(?string $valueName = null, $value = null): void;

    public function has(?string $valueName = null): bool;
}
