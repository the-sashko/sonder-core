<?php

namespace Sonder\Middlewares;

use Exception;
use Sonder\Core\CoreMiddleware;
use Sonder\Core\Interfaces\IMiddleware;

final class RouterMiddleware extends CoreMiddleware implements IMiddleware
{
    const DEFAULT_ROUTING_TYPE = 'default';

    const DEFAULT_CONTROLLER = 'main';

    const DEFAULT_METHOD = 'index';

    const ANNOTATIONS_ROUTING_TYPE = 'annotations';

    /**
     * @throws Exception
     */
    final public function run(): void
    {
        if (
            APP_ROUTING_TYPE != RouterMiddleware::DEFAULT_ROUTING_TYPE &&
            APP_ROUTING_TYPE != RouterMiddleware::ANNOTATIONS_ROUTING_TYPE
        ) {
            throw new Exception(sprintf(
                'Routing Type %s Is Not Supporting',
                APP_ROUTING_TYPE
            ));
        }

        if (
            (
                empty($this->request->getController()) ||
                empty($this->request->getMethod())
            ) &&
            APP_ROUTING_TYPE == 'annotations'
        ) {
            $this->_setRouteByAnnotations();
        }

        if (
            (
                empty($this->request->getController()) ||
                empty($this->request->getMethod())
            ) &&
            APP_ROUTING_TYPE == 'default'
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
