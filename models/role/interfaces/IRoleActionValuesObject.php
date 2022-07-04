<?php

namespace Sonder\Models\Role\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelValuesObject;

#[IModelValuesObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface IRoleActionValuesObject extends IModelValuesObject
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return bool
     */
    public function isSystem(): bool;

    /**
     * @return string
     */
    public function getAdminViewLink(): string;

    /**
     * @param string|null $name
     * @return void
     */
    public function setName(?string $name = null): void;
}
