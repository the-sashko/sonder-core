<?php

namespace Sonder\Plugins\Router\Interfaces;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
interface IRouterPlugin
{
    /**
     * @param string|null $url
     * @return IRouterEntity|null
     */
    public function getRoute(string $url = null): ?IRouterEntity;

    /**
     * @return void
     */
    public function cleanCache(): void;
}
