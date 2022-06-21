<?php

namespace Sonder\Endpoints;

use Sonder\Core\CoreEndpoint;
use Sonder\Enums\MiddlewaresEnum;
use Sonder\Interfaces\ICliEndpoint;
use Sonder\Interfaces\IEndpoint;

#[IEndpoint]
#[ICliEndpoint]
final class CliEndpoint extends CoreEndpoint implements ICliEndpoint
{
    /**
     * @var array
     */
    protected array $middlewares = [
        MiddlewaresEnum::CLI
    ];
}
