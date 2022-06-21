<?php

namespace Sonder\Models\Config\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelValuesObject;

#[IModelValuesObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface IConfigValuesObject extends IModelValuesObject
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getFilePath(): string;

    /**
     * @return array|null
     */
    public function getValues(): ?array;

    /**
     * @return string
     */
    public function getViewLink(): string;

    /**
     * @return string
     */
    public function getEditLink(): string;

    /**
     * @param string|null $name
     * @return void
     */
    public function setName(?string $name = null): void;

    /**
     * @param string|null $filePath
     * @return void
     */
    public function setFilePath(?string $filePath = null): void;

    /**
     * @param array|null $values
     * @return void
     */
    public function setValues(?array $values = null): void;

    /**
     * @return array
     */
    public function exportRow(): array;
}
