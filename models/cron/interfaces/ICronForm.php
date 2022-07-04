<?php

namespace Sonder\Models\Cron\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelFormObject;

#[IModelFormObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface ICronForm extends IModelFormObject
{
    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @return string|null
     */
    public function getAlias(): ?string;

    /**
     * @return string|null
     */
    public function getController(): ?string;

    /**
     * @return string|null
     */
    public function getControllerMethod(): ?string;

    /**
     * @return int|null
     */
    public function getInterval(): ?int;

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
     * @param string|null $alias
     * @return void
     */
    public function setAlias(?string $alias = null): void;

    /**
     * @param string|null $controller
     * @return void
     */
    public function setController(?string $controller = null): void;

    /**
     * @param string|null $controllerMethod
     * @return void
     */
    public function setControllerMethod(?string $controllerMethod = null): void;

    /**
     * @param int|null $interval
     * @return void
     */
    public function setInterval(?int $interval = null): void;

    /**
     * @param bool $isActive
     * @return void
     */
    public function setIsActive(bool $isActive = false): void;
}
