<?php

namespace Sonder\Models\User\Interfaces;

use Attribute;
use Sonder\Interfaces\IModel;
use Sonder\Models\Role\Interfaces\IRoleSimpleValuesObject;
use Sonder\Interfaces\IRoleValuesObject;

#[IModel]
#[Attribute(Attribute::TARGET_CLASS)]
interface IUserModel extends IModel
{
    /**
     * @param string|null $apiToken
     * @return void
     */
    public function signInByApiToken(?string $apiToken = null): void;

    /**
     * @param string|null $login
     * @param string|null $password
     * @return bool
     */
    public function signInByLoginAndPassword(
        ?string $login = null,
        ?string $password = null
    ): bool;

    /**
     * @return bool
     */
    public function signOut(): bool;

    /**
     * @return bool
     */
    public function isSignedIn(): bool;

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return IUserValuesObject|null
     */
    public function getVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?IUserValuesObject;

    /**
     * @param int|null $id
     * @return IUserSimpleValuesObject|null
     */
    public function getSimpleVOById(?int $id = null): ?IUserSimpleValuesObject;

    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @return string|null
     */
    public function getLogin(): ?string;

    /**
     * @return IRoleValuesObject
     */
    public function getRole(): IRoleValuesObject;

    /**
     * @return IRoleSimpleValuesObject
     */
    public function getRoleSimpleVO(): IRoleSimpleValuesObject;

    /**
     * @param int $page
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    public function getUsersByPage(
        int $page,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array;

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    public function getUsersPageCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int;

    /**
     * @param int|null $id
     * @return bool
     */
    public function removeById(?int $id = null): bool;

    /**
     * @param int|null $id
     * @return bool
     */
    public function restoreById(?int $id = null): bool;

    /**
     * @param IUserForm $userForm
     * @return bool
     */
    public function save(IUserForm $userForm): bool;

    /**
     * @param ICredentialsForm $credentialsForm
     * @return bool
     */
    public function saveCredentials(ICredentialsForm $credentialsForm): bool;
}
