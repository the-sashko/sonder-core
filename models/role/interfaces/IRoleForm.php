<?php

namespace Sonder\Models\Role\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelFormObject;

#[IModelFormObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface IRoleForm extends IModelFormObject
{
    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @return int|null
     */
    public function getParentId(): ?int;

    /**
     * @return string|null
     */
    public function getName(): ?string;

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
    public function isActive(): bool;

    /**
     * @param int|null $id
     * @return void
     */
    public function setId(?int $id = null): void;

    /**
     * @param string|null $name
     * @return void
     */
    public function setName(?string $name = null): void;

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
     * @param bool $isActive
     * @return void
     */
    public function setIsActive(bool $isActive = false): void;
}
