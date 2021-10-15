<?php
namespace Sonder\Middlewares;

use Sonder\Core\CoreMiddleware;
use Sonder\Core\Interfaces\IMiddleware;

final class ApiMiddleware extends CoreMiddleware implements IMiddleware
{
    const API_CONTROLLER = 'api';
    const API_CONTROLLER_METHOD = 'execute';

    final public function run(): void
    {
        $this->request->setController(ApiMiddleware::API_CONTROLLER);
        $this->request->setMethod(ApiMiddleware::API_CONTROLLER_METHOD);
    }
}
