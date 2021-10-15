<?php

namespace Sonder\Core\Interfaces;

interface IMiddleware
{
    public function run(): void;
}
