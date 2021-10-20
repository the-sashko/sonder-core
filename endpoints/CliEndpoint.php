<?php
namespace Sonder\Endpoints;

use Sonder\Core\CoreEndpoint;
use Sonder\Core\Interfaces\IEndpoint;

final class CliEndpoint extends CoreEndpoint implements IEndpoint
{
    /**
     * @var array
     */
    protected array $middlewares = [
        'cli'
    ];
}
