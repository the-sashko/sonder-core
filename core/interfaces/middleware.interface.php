<?php

namespace Sonder\Core\Interfaces;

use Sonder\Core\RequestObject;

interface IMiddleware
{
    public function getRequest(): RequestObject;
}
