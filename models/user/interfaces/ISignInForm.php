<?php

namespace Sonder\Models\User\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelFormObject;

#[IModelFormObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface ISignInForm extends IModelFormObject
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
    public function getPassword(): ?string;

    /**
     * @param string|null $login
     * @return void
     */
    public function setLogin(?string $login = null): void;
}
