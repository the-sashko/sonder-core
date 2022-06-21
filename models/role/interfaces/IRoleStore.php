<?php

namespace Sonder\Models\Role\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelStore;

#[IModelStore]
#[Attribute(Attribute::TARGET_CLASS)]
interface IRoleStore extends IModelStore
{
    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getRoleRowById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param string|null $name
     * @param int|null $excludeId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getRoleRowByName(
        ?string $name = null,
        ?int $excludeId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param int $page
     * @param int $limit
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getRoleRowsByPage(
        int $page = 1,
        int $limit = 10,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @return array|null
     */
    public function getAllRoleRows(): ?array;

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getRoleActionRowById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param int|null $roleId
     * @return array|null
     */
    public function getAllowedActionRowsByRoleId(?int $roleId = null): ?array;

    /**
     * @param int|null $roleId
     * @return array|null
     */
    public function getDeniedActionRowsByRoleId(?int $roleId = null): ?array;

    /**
     * @param int|null $roleId
     * @param bool|null $isAllowed
     * @return array|null
     */
    public function getActionRowsByRoleId(
        ?int $roleId = null,
        ?bool $isAllowed = null
    ): ?array;

    /**
     * @param string|null $name
     * @param int|null $excludeId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getRoleActionRowByName(
        ?string $name = null,
        ?int $excludeId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param string|null $name
     * @return int|null
     */
    public function getRoleActionIdByName(?string $name = null): ?int;

    /**
     * @param string|null $name
     * @return int|null
     */
    public function getRoleIdByName(?string $name = null): ?int;

    /**
     * @param int $page
     * @param int $limit
     * @return array|null
     */
    public function getRoleActionRowsByPage(
        int $page = 1,
        int $limit = 10
    ): ?array;

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    public function getRoleActionRowsCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int;

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    public function getRoleRowsCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int;

    /**
     * @return array|null
     */
    public function getAllRoleActionRows(): ?array;

    /**
     * @param int|null $id
     * @param bool $isSoftDelete
     * @return bool
     */
    public function deleteRoleById(
        ?int $id = null,
        bool $isSoftDelete = true
    ): bool;

    /**
     * @param int|null $id
     * @param bool $isSoftDelete
     * @return bool
     */
    public function deleteRoleActionById(
        ?int $id = null,
        bool $isSoftDelete = true
    ): bool;

    /**
     * @param int|null $id
     * @return bool
     */
    public function restoreRoleActionById(?int $id = null): bool;

    /**
     * @param array|null $row
     * @param string|null $condition
     * @return bool
     */
    public function updateRoleAction(
        ?array $row = null,
        ?string $condition = null
    ): bool;

    /**
     * @param string|null $condition
     * @return bool
     */
    public function deleteRoleAction(?string $condition = null): bool;

    /**
     * @param int|null $id
     * @return bool
     */
    public function restoreRoleById(?int $id = null): bool;

    /**
     * @param IRoleActionValuesObject|null $roleActionVO
     * @return bool
     */
    public function insertOrUpdateRoleAction(
        ?IRoleActionValuesObject $roleActionVO = null
    ): bool;

    /**
     * @param array|null $row
     * @return bool
     */
    public function insertRoleAction(?array $row = null): bool;

    /**
     * @param int|null $roleId
     * @param int|null $roleActionId
     * @param bool $isAllowed
     * @return bool
     */
    public function insertRoleToRoleAction(
        ?int $roleId = null,
        ?int $roleActionId = null,
        bool $isAllowed = true
    ): bool;

    /**
     * @param int|null $roleId
     * @return bool
     */
    public function deleteRoleToRoleActionByRoleId(?int $roleId = null): bool;

    /**
     * @param array|null $row
     * @return bool
     */
    public function insertRole(?array $row = null): bool;

    /**
     * @param array|null $row
     * @param int|null $id
     * @return bool
     */
    public function updateRoleActionById(
        ?array $row = null,
        ?int $id = null
    ): bool;

    /**
     * @param array|null $row
     * @param int|null $id
     * @return bool
     */
    public function updateRoleById(?array $row = null, ?int $id = null): bool;

    /**
     * @param IRoleValuesObject|null $roleVO
     * @return bool
     */
    public function insertOrUpdateRole(?IRoleValuesObject $roleVO = null): bool;
}
