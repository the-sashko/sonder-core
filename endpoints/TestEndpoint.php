<?php
namespace Sonder\Endpoints;

use Sonder\Core\CoreEndpoint;
use Sonder\Core\Interfaces\IEndpoint;

class TestEndpoint extends CoreEndpoint implements IEndpoint
{
    /**
     * @var array|null
     */
    private ?array $_middlewares = [
        'session'
    ];
}
