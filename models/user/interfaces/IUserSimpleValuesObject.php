<?php

namespace Sonder\Models\User\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelSimpleValuesObject;
use Sonder\Models\Role\Interfaces\IRoleSimpleValuesObject;

#[IModelSimpleValuesObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface IUserSimpleValuesObject extends IModelSimpleValuesObject
{
    /**
     * @return string|null
     */
    public function getLogin(): ?string;

    /**
     * @return string|null
     */
    public function getEmail(): ?string;

    /**
     * @return int|null
     */
    public function getRoleId(): ?int;

    /**
     * @return IRoleSimpleValuesObject|null
     */
    public function getRoleVO(): ?IRoleSimpleValuesObject;

    /**
     * @param IRoleSimpleValuesObject|null $roleVO
     * @return void
     */
    function setRoleVO(?IRoleSimpleValuesObject $roleVO = null): void;

    /**
     * @return array
     */
    public function exportRow(): array;
}
