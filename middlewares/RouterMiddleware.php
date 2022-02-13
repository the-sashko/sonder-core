<?php

namespace Sonder\Middlewares;

use Exception;
use Sonder\Core\CoreMiddleware;
use Sonder\Core\Interfaces\IMiddleware;
use Sonder\Exceptions\AppException;
use Sonder\Exceptions\MiddlewareException;

final class RouterMiddleware extends CoreMiddleware implements IMiddleware
{
    const DEFAULT_ROUTING_TYPE = 'default';

    const DEFAULT_CONTROLLER = 'main';

    const DEFAULT_METHOD = 'displayIndex';

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
            $errorMessage = sprintf(
                MiddlewareException::MESSAGE_MIDDLEWARE_ROUTING_TYPE_IS_NOT_SUPPORTED,
                APP_ROUTING_TYPE
            );

            throw new MiddlewareException(
                $errorMessage,
                AppException::CODE_MIDDLEWARE_ROUTING_TYPE_IS_NOT_SUPPORTED
            );
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

    /**
     * @throws Exception
     */
    private function _setRouteByAnnotations(): void
    {
        $securityPlugin = $this->getPlugin('security');

        $controllersPaths = [
            APP_PROTECTED_DIR_PATH . '/controllers',
            APP_FRAMEWORK_DIR_PATH . '/controllers'
        ];

        if (
            array_key_exists('controllers', APP_SOURCE_PATHS) &&
            is_array(APP_SOURCE_PATHS['controllers'])
        ) {
            $controllersPaths = APP_SOURCE_PATHS['controllers'];
        }

        $routerPlugin = $this->getPlugin('router', $controllersPaths);

        $routerPlugin->cleanCache();

        $controller = RouterMiddleware::DEFAULT_CONTROLLER;
        $method = RouterMiddleware::DEFAULT_METHOD;

        $route = $routerPlugin->getRoute($this->request->getUrl());

        if (
            !empty($route) &&
            file_exists($route->getControllerFilePath()) &&
            is_file($route->getControllerFilePath())
        ) {
            require_once $route->getControllerFilePath();

            $controller = $route->getController();
            $controller = explode('\\', $controller);
            $controller = end($controller);

            $controller = preg_replace(
                '/^(.*?)Controller$/su',
                '$1',
                $controller
            );

            $method = $route->getMethod();

            $urlValues = (array)$route->getParams();

            $urlValues = array_map(
                [
                    $securityPlugin,
                    'escapeInput'
                ],
                $urlValues
            );

            $urlValues = array_merge(
                (array)$this->request->getUrlValues(),
                $urlValues
            );

            $this->request->setUrlValues($urlValues);
            $this->request->setLanguage($route->getLanguage());
            $this->request->setNoCache($route->getNoCache());
        }

        $this->request->setController($controller);
        $this->request->setMethod($method);
    }

    /**
     * @return void
     */
    private function _setRouteByUrlParams(): void
    {
        $controller = $this->request->getUrlValue('controller');
        $method = $this->request->getUrlValue('method');

        if (!empty($method)) {
            $method = sprintf(
                'display%s',
                mb_convert_case($method, MB_CASE_TITLE)
            );
        }

        if (empty($controller)) {
            $controller = RouterMiddleware::DEFAULT_CONTROLLER;
        }

        if (empty($method)) {
            $method = RouterMiddleware::DEFAULT_METHOD;
        }

        $this->request->setController($controller);
        $this->request->setMethod($method);
    }
}
