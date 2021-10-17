<?php

namespace Sonder\Plugins\Router\Interfaces;

interface IRouterPlugin
{
    /**
     * @param string|null $url
     *
     * @return IRouterEntity|null
     */
    public function getRoute(?string $url = null): ?IRouterEntity;

    public function cleanCache(): void;
}
