<?php

namespace Sonder\Models\Role\Interfaces;

use Attribute;
use Sonder\Interfaces\IModel;
use Sonder\Interfaces\IRoleValuesObject as IRoleValuesObjectFramework;

#[IModel]
#[Attribute(Attribute::TARGET_CLASS)]
interface IRoleModel extends IModel
{
    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return IRoleValuesObjectFramework|null
     */
    public function getVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?IRoleValuesObjectFramework;

    /**
     * @param int $page
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getRolesByPage(
        int $page,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return IRoleActionValuesObject|null
     */
    public function getRoleActionVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?IRoleActionValuesObject;

    /**
     * @param int|null $id
     * @return IRoleActionSimpleValuesObject|null
     */
    public function getRoleActionSimpleVOById(
        ?int $id = null
    ): ?IRoleActionSimpleValuesObject;

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return IRoleValuesObject|null
     */
    public function getRoleVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?IRoleValuesObject;

    /**
     * @param int|null $id
     * @return IRoleSimpleValuesObject|null
     */
    public function getRoleSimpleVOById(
        ?int $id = null
    ): ?IRoleSimpleValuesObject;

    /**
     * @param int $page
     * @return array|null
     */
    public function getRoleActionsByPage(
        int $page
    ): ?array;

    /**
     * @return array|null
     */
    public function getAllRoleActions(): ?array;

    /**
     * @return array|null
     */
    public function getAllRoles(): ?array;

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    public function getRolesPageCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int;

    /**
     * @return int
     */
    public function getRoleActionsPageCount(): int;

    /**
     * @return IRoleValuesObjectFramework
     */
    public function getGuestVO(): IRoleValuesObjectFramework;

    /**
     * @return IRoleSimpleValuesObject
     */
    public function getGuestSimpleVO(): IRoleSimpleValuesObject;

    /**
     * @param int|null $roleId
     * @return array|null
     */
    public function getAllowedActionsByRoleId(?int $roleId = null): ?array;

    /**
     * @param int|null $roleId
     * @return array|null
     */
    public function getDeniedActionsByRoleId(?int $roleId = null): ?array;

    /**
     * @param IRoleActionForm $roleActionForm
     * @return bool
     */
    public function saveRoleAction(IRoleActionForm $roleActionForm): bool;

    /**
     * @param IRoleForm $roleForm
     * @return bool
     */
    public function saveRole(IRoleForm $roleForm): bool;

    /**
     * @param int|null $id
     * @return bool
     */
    public function removeRoleActionById(?int $id = null): bool;

    /**
     * @param int|null $id
     * @return bool
     */
    public function restoreRoleActionById(?int $id = null): bool;

    /**
     * @param int|null $id
     * @return bool
     */
    public function removeRoleById(?int $id = null): bool;

    /**
     * @param int|null $id
     * @return bool
     */
    public function restoreRoleById(?int $id = null): bool;
}
