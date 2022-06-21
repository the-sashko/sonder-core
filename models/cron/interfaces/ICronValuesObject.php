<?php

namespace Sonder\Models\Cron\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelValuesObject;

#[IModelValuesObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface ICronValuesObject extends IModelValuesObject
{
    /**
     * @return string
     */
    public function getAlias(): string;

    /**
     * @return string
     */
    public function getController(): string;

    /**
     * @return string
     */
    public function getControllerMethod(): string;

    /**
     * @param bool $isFormatAsString
     * @return string|int|null
     */
    public function getInterval(
        bool $isFormatAsString = false
    ): string|int|null;

    /**
     * @param string|null $format
     * @return string|int|null
     */
    public function getTimeNextExec(?string $format = null): string|int|null;

    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @return string|null
     */
    public function getErrorMessage(): ?string;

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
    public function getAdminRunLink(): string;

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
     * @param string|null $method
     * @return void
     */
    public function setControllerMethod(?string $method = null): void;

    /**
     * @param int|null $interval
     * @return void
     */
    public function setInterval(?int $interval = null): void;

    /**
     * @return void
     */
    public function setTimeNextExec(): void;

    /**
     * @param string|null $status
     * @return void
     */
    public function setStatus(?string $status = null): void;

    /**
     * @param string|null $errorMessage
     * @return void
     */
    public function setErrorMessage(?string $errorMessage = null): void;
}
