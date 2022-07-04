<?php

namespace Sonder\Models\User\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelFormObject;

#[IModelFormObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface IUserForm extends IModelFormObject
{
    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @return int|null
     */
    public function getRoleId(): ?int;

    /**
     * @return string|null
     */
    public function getLogin(): ?string;

    /**
     * @return string|null
     */
    public function getEmail(): ?string;

    /**
     * @return string|null
     */
    public function getPassword(): ?string;

    /**
     * @return bool
     */
    public function isAllowAccessByApi(): bool;

    /**
     * @return bool
     */
    public function isActive(): bool;

    /**
     * @param int|null $id
     * @return void
     */
    public function setId(?int $id = null): void;

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
     * @param bool $isAllowAccessByApi
     * @return void
     */
    public function setIsAllowAccessByApi(
        bool $isAllowAccessByApi = false
    ): void;

    /**
     * @param bool $isActive
     * @return void
     */
    public function setIsActive(bool $isActive = false): void;
}
