<?php

namespace Sonder\Plugins\Router\Interfaces;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
interface IRouterEntity
{
    /**
     * @return string
     */
    public function getArea(): string;

    /**
     * @return string
     */
    public function getRoute(): string;

    /**
     * @return string
     */
    public function getRoutePattern(): string;

    /**
     * @return array|null
     */
    public function getParams(): ?array;

    /**
     * @return bool
     */
    public function getNoCache(): bool;

    /**
     * @return string
     */
    public function getControllerFilePath(): string;

    /**
     * @return string
     */
    public function getController(): string;

    /**
     * @return string
     */
    public function getControllerMethod(): string;

    /**
     * @return string|null
     */
    public function getLanguage(): ?string;

    /**
     * @param string|null $language
     * @return void
     */
    public function setLanguage(?string $language = null): void;
}
