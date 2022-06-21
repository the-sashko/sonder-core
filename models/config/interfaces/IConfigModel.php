<?php

namespace Sonder\Models\Config\Interfaces;

use Attribute;
use Sonder\Interfaces\IModel;

#[IModel]
#[Attribute(Attribute::TARGET_CLASS)]
interface IConfigModel extends IModel
{
    /**
     * @return array
     */
    public function getConfigs(): array;

    /**
     * @param string|null $name
     * @return IConfigValuesObject|null
     */
    public function getConfig(?string $name = null): ?IConfigValuesObject;

    /**
     * @param string $name
     * @param array $inputValues
     * @return array
     */
    public function updateConfig(string $name, array $inputValues): array;
}
