<?php

namespace Sonder\Endpoints;

use Sonder\Core\CoreEndpoint;
use Sonder\Enums\MiddlewaresEnum;
use Sonder\Interfaces\IApiEndpoint;
use Sonder\Interfaces\IEndpoint;

#[IEndpoint]
#[IApiEndpoint]
final class ApiEndpoint extends CoreEndpoint implements IApiEndpoint
{
    /**
     * @var array
     */
    protected array $middlewares = [
        MiddlewaresEnum::API
    ];
}
