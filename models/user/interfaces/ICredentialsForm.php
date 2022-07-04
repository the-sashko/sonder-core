<?php

namespace Sonder\Models\User\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelFormObject;

#[IModelFormObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface ICredentialsForm extends IModelFormObject
{
    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @return string|null
     */
    public function getLogin(): ?string;

    /**
     * @return string|null
     */
    public function getPassword(): ?string;

    /**
     * @return bool
     */
    public function isAllowAccessByApi(): bool;

    /**
     * @param string|null $login
     * @return void
     */
    public function setLogin(?string $login = null): void;

    /**
     * @param bool $isAllowAccessByApi
     * @return void
     */
    public function setIsAllowAccessByApi(
        bool $isAllowAccessByApi = false
    ): void;
}
