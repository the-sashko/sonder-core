<?php

namespace Sonder\Models\Role\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelSimpleValuesObject;

#[IModelSimpleValuesObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface IRoleSimpleValuesObject extends IModelSimpleValuesObject
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return int|null
     */
    public function getParentId(): ?int;

    /**
     * @return IRoleSimpleValuesObject|null
     */
    public function getParentVO(): ?IRoleSimpleValuesObject;

    /**
     * @return array|null
     */
    public function getAllowedActions(): ?array;

    /**
     * @return array|null
     */
    public function getDeniedActions(): ?array;

    /**
     * @param string|null $roleActionIdent
     * @return bool
     */
    function can(?string $roleActionIdent = null): bool;

    /**
     * @param IRoleSimpleValuesObject|null $parentVO
     * @return void
     */
    public function setParentVO(
        ?IRoleSimpleValuesObject $parentVO = null
    ): void;

    /**
     * @param array|null $allowedActions
     * @return void
     */
    public function setAllowedActions(?array $allowedActions = null): void;

    /**
     * @param array|null $deniedActions
     * @return void
     */
    public function setDeniedActions(?array $deniedActions = null): void;

    /**
     * @return array
     */
    public function exportRow(): array;
}
