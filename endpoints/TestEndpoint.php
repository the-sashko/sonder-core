<?php

namespace Sonder\Endpoints;

use Sonder\Core\CoreEndpoint;
use Sonder\Enums\MiddlewaresEnum;
use Sonder\Interfaces\IEndpoint;
use Sonder\Interfaces\ITestEndpoint;

#[IEndpoint]
#[ITestEndpoint]
final class TestEndpoint extends CoreEndpoint implements ITestEndpoint
{
    /**
     * @var array
     */
    protected array $middlewares = [
        MiddlewaresEnum::SESSION
    ];
}
