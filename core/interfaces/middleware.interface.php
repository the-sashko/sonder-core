<?php
namespace SonderCore\Core\Interfaces;

use SonderCore\Core\RequestObject;

interface IMiddleware
{
    public function getRequest(): RequestObject;
}
