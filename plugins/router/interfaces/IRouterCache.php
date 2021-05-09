<?php
namespace Core\Plugins\Router\Interfaces;

interface IRouterCache
{
    public function getRoutes(): ?array;

    public function getRouteByUrl(string $url): ?IRouterEntity;

    public function saveRoutes(?array $routes = null): void;

    public function saveRouteUrl(
        ?string        $url   = null,
        ?IRouterEntity $route = null
    ): void;

    public function clean(): void;
}
