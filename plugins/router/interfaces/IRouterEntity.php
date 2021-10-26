<?php

namespace Sonder\Plugins\Router\Interfaces;

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
     * @return string
     */
    public function getController(): string;

    /**
     * @return string
     */
    public function getControllerFilePath(): string;

    /**
     * @return string
     */
    public function getMethod(): string;

    /**
     * @return string|null
     */
    public function getLanguage(): ?string;

    /**
     * @return bool
     */
    public function getNoCache(): bool;

    /**
     * @param string|null $language
     */
    public function setLanguage(?string $language = null): void;

    /**
     * @param array|null $params
     */
    public function setParams(?array $params = null): void;
}
