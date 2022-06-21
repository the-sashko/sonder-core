<?php

namespace Sonder\Plugins\Router\Interfaces;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
interface IRouterCache
{
    /**
     * @return array|null
     */
    public function getRoutes(): ?array;

    /**
     * @param string $url
     * @return IRouterEntity|null
     */
    public function getRouteByUrl(string $url): ?IRouterEntity;

    /**
     * @param array $routes
     * @return void
     */
    public function saveRoutes(array $routes): void;

    /**
     * @param string $url
     * @param IRouterEntity $route
     * @return void
     */
    public function saveRouteUrl(string $url, IRouterEntity $route): void;

    /**
     * @return void
     */
    public function clean(): void;
}
