<?php

namespace Sonder\Core\Interfaces;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
interface ICoreObject
{
    /**
     * @param string $pluginName
     * @param mixed ...$pluginValues
     * @return object
     */
    public static function getPlugin(
        string $pluginName,
        mixed  ...$pluginValues
    ): object;
}
