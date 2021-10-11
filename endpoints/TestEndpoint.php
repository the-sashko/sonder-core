<?php
namespace SonderCore\Endpoints;

use SonderCore\Core\CoreEndpoint;
use SonderCore\Core\Interfaces\IEndpoint;

class TestEndpoint extends CoreEndpoint implements IEndpoint
{
    /**
     * @var array|null
     */
    private ?array $_middlewares = [
        'security',
        'session',
        'router'
    ];
}
