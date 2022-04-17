<?php

namespace Sonder\Plugins\Router\Interfaces;

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
     * @param array|null $routes
     */
    public function saveRoutes(?array $routes = null): void;

    /**
     * @param string|null $url
     * @param IRouterEntity|null $route
     */
    public function saveRouteUrl(
        ?string        $url = null,
        ?IRouterEntity $route = null
    ): void;

    public function clean(): void;
}
