<?php

namespace Sonder\Core\Interfaces;

use Sonder\Core\RequestObject;

interface IMiddleware
{
    public function run(): void;
}
