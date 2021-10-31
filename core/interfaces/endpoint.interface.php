<?php

namespace Sonder\Core\Interfaces;

interface IEndpoint
{
    /**
     * @param array|null $middlewares
     */
    public function run(?array $middlewares = null): void;
}
