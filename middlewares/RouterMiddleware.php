<?php
namespace Sonder\Middlewares;

use Sonder\Core\CoreMiddleware;
use Sonder\Core\Interfaces\IMiddleware;

final class RouterMiddleware extends CoreMiddleware implements IMiddleware
{
    const DEFAULT_CONTROLLER = 'main';

    const DEFAULT_METHOD = 'index';

    final public function run(): void
    {
        $controller = $this->request->getUrlValue('controller');
        $method = $this->request->getUrlValue('method');

        if (empty($controller)) {
            $controller = RouterMiddleware::DEFAULT_CONTROLLER;
        }

        if (empty($method)) {
            $method = RouterMiddleware::DEFAULT_METHOD;
        }

        $method = sprintf(
            'display%s',
            mb_convert_case($method, MB_CASE_TITLE)
        );

        $this->request->setController($controller);
        $this->request->setMethod($method);
    }
}
