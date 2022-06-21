<?php

namespace Sonder\Models\User\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelValuesObject;
use Sonder\Models\Role\Interfaces\IRoleValuesObject;

#[IModelValuesObject]
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
interface IUserValuesObject extends IModelValuesObject
{
    /**
     * @return string
     */
    public function getLogin(): string;

    /**
     * @return string
     */
    public function getEmail(): string;

    /**
     * @return int
     */
    public function getRoleId(): int;

    /**
     * @return IRoleValuesObject|null
     */
    public function getRoleVO(): ?IRoleValuesObject;

    /**
     * @return string
     */
    public function getPasswordHash(): string;

    /**
     * @return string|null
     */
    public function getApiToken(): ?string;

    /**
     * @return string
     */
    public function getWebToken(): string;

    /**
     * @param string|null $format
     * @return string|int|null
     */
    public function getLastSignInDate(?string $format = null): string|int|null;

    /**
     * @return string
     */
    public function getEditLink(): string;

    /**
     * @return string
     */
    public function getAdminViewLink(): string;

    /**
     * @return string
     */
    public function getAdminCredentialsLink(): string;

    /**
     * @param string|null $login
     * @return void
     */
    public function setLogin(?string $login = null): void;

    /**
     * @param string|null $email
     * @return void
     */
    public function setEmail(?string $email = null): void;

    /**
     * @param int|null $roleId
     * @return void
     */
    public function setRoleId(?int $roleId = null): void;

    /**
     * @param IRoleValuesObject|null $roleVO
     * @return void
     */
    public function setRoleVO(?IRoleValuesObject $roleVO = null): void;

    /**
     * @param string|null $passwordHash
     * @return void
     */
    public function setPasswordHash(?string $passwordHash = null): void;

    /**
     * @param string|null $apiToken
     * @return void
     */
    public function setApiToken(?string $apiToken = null): void;

    /**
     * @param string|null $webToken
     * @return void
     */
    public function setWebToken(?string $webToken = null): void;

    /**
     * @return void
     */
    public function setLastLoginDate(): void;
}
