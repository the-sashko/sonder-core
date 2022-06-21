<?php

namespace Sonder\Models\User\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelStore;

#[IModelStore]
#[Attribute(Attribute::TARGET_CLASS)]
interface IUserStore extends IModelStore
{
    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getUserRowById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param string|null $login
     * @return int|null
     */
    public function getUserIdByLogin(?string $login = null): ?int;

    /**
     * @param string|null $login
     * @param int|null $excludeId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getUserRowByLogin(
        ?string $login = null,
        ?int $excludeId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param string|null $email
     * @param int|null $excludeId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getUserRowByEmail(
        ?string $email = null,
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
    public function getAllUsers(
        int $page = 1,
        int $limit = 10,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param string|null $apiToken
     * @return array|null
     */
    public function getRowByApiToken(?string $apiToken = null): ?array;

    /**
     * @param string|null $webToken
     * @param int|null $id
     * @return array|null
     */
    public function getRowByWebTokenAndId(
        ?string $webToken = null,
        ?int $id = null,
    ): ?array;

    /**
     * @param string|null $login
     * @param string|null $passwordHash
     * @return array|null
     */
    public function getRowByLoginAndPasswordHash(
        ?string $login = null,
        ?string $passwordHash = null
    ): ?array;

    /**
     * @param string|null $webToken
     * @param int|null $id
     * @param int|null $lastLoginDate
     * @return bool
     */
    public function updateWebTokenById(
        ?string $webToken = null,
        ?int $id = null,
        ?int $lastLoginDate = null
    ): bool;

    /**
     * @param array|null $row
     * @param int|null $id
     * @return bool
     */
    public function updateUserById(
        ?array $row = null,
        ?int $id = null
    ): bool;

    /**
     * @param int|null $id
     * @param bool $isSoftDelete
     * @return bool
     */
    public function deleteUserById(
        ?int $id = null,
        bool $isSoftDelete = true
    ): bool;

    /**
     * @param int|null $id
     * @return bool
     */
    public function restoreUserById(?int $id = null): bool;

    /**
     * @param int $page
     * @param int $limit
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getUserRowsByPage(
        int $page = 1,
        int $limit = 10,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    public function getUserRowsCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int;

    /**
     * @param IUserValuesObject|null $userVO
     * @return bool
     */
    public function insertOrUpdateUser(?IUserValuesObject $userVO = null): bool;

    /**
     * @param array|null $row
     * @return bool
     */
    public function insertUser(?array $row = null): bool;
}
