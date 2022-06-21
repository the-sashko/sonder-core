<?php

namespace Sonder\Interfaces;

use Attribute;
use Sonder\Enums\ConfigNamesEnum;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
interface IConfigObject
{
    /**
     * @param ConfigNamesEnum|string $configName
     * @return array
     */
    public function get(ConfigNamesEnum|string $configName): array;

    /**
     * @param ConfigNamesEnum|string $configName
     * @param string $valueName
     * @return string
     */
    public function getValue(
        ConfigNamesEnum|string $configName,
        string $valueName
    ): string;

    /**
     * @param string|ConfigNamesEnum $configName
     * @param string $valueName
     * @return bool
     */
    public function hasValue(
        string|ConfigNamesEnum $configName,
        string $valueName
    ): bool;
}
