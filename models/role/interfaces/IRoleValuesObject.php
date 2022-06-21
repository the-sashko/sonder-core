<?php

namespace Sonder\Models\Role\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelValuesObject;
use Sonder\Interfaces\IRoleValuesObject as IRoleValuesObjectFramework;

#[IModelValuesObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface IRoleValuesObject extends IModelValuesObject
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
     * @return IRoleValuesObjectFramework|null
     */
    public function getParentVO(): ?IRoleValuesObjectFramework;

    /**
     * @return array|null
     */
    public function getAllowedActions(): ?array;

    /**
     * @return array|null
     */
    public function getDeniedActions(): ?array;

    /**
     * @return bool
     */
    public function isSystem(): bool;

    /**
     * @return string
     */
    public function getEditLink(): string;

    /**
     * @return string
     */
    public function getAdminViewLink(): string;

    /**
     * @param string|null $roleActionIdent
     * @return bool
     */
    public function can(?string $roleActionIdent = null): bool;

    /**
     * @param string|null $name
     * @return void
     */
    public function setName(?string $name = null): void;

    /**
     * @param int|null $parentId
     * @return void
     */
    public function setParentId(?int $parentId = null): void;

    /**
     * @param IRoleValuesObjectFramework|null $parentVO
     * @return void
     */
    public function setParentVO(?IRoleValuesObjectFramework $parentVO = null): void;

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
