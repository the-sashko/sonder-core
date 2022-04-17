<?php

namespace Sonder\Endpoints;

use Sonder\Core\CoreEndpoint;
use Sonder\Core\Interfaces\IEndpoint;

final class TestEndpoint extends CoreEndpoint implements IEndpoint
{
    /**
     * @var array
     */
    protected array $middlewares = [
        'session'
    ];
}
