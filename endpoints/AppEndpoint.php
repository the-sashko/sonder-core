<?php

namespace Sonder\Endpoints;

use Sonder\Core\CoreEndpoint;
use Sonder\Enums\MiddlewaresEnum;
use Sonder\Interfaces\IAppEndpoint;
use Sonder\Interfaces\IEndpoint;

#[IEndpoint]
#[IAppEndpoint]
final class AppEndpoint extends CoreEndpoint implements IAppEndpoint
{
    protected const CACHE_TTL = 300;

    /**
     * @var array
     */
    protected array $middlewares = [
        MiddlewaresEnum::SESSION
    ];
}
