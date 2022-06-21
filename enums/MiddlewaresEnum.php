<?php

namespace Sonder\Enums;

use Sonder\Core\Interfaces\ICoreEnum;
use Sonder\Interfaces\IMiddlewaresEnum;

#[ICoreEnum]
#[IMiddlewaresEnum]
enum MiddlewaresEnum: string implements IMiddlewaresEnum
{
    case API = 'api';
    case CLI = 'cli';
    case ROUTER = 'router';
    case SECURITY = 'security';
    case SESSION = 'session';

    final public const DEFAULT_MIDDLEWARES = [
        MiddlewaresEnum::SESSION
    ];
}
