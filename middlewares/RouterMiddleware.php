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
        if (
            empty($this->request->getController()) ||
            empty($this->request->getMethod())
        ) {
            $this->_setRouteByAnnotations();
        }

        if (
            empty($this->request->getController()) ||
            empty($this->request->getMethod())
        ) {
            $this->_setRouteByUrlParams();
        }
    }

    private function _setRouteByAnnotations(): void
    {
        //TODO
    }

    private function _setRouteByUrlParams(): void
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
