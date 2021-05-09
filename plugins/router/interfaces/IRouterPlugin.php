<?php
namespace Core\Plugins\Router\Interfaces;

interface IRouterPlugin
{
    public function getRoute(?string $url = null): ?IRouterEntity;

    public function cleanCache(): void;
}
