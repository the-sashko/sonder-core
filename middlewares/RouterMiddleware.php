<?php

namespace Sonder\Middlewares;

use Sonder\Core\CoreMiddleware;
use Sonder\Exceptions\CoreException;
use Sonder\Interfaces\IMiddleware;
use Sonder\Exceptions\AppException;
use Sonder\Exceptions\MiddlewareException;
use Sonder\Interfaces\IRouterMiddleware;
use Sonder\Plugins\Annotation\AnnotationException;
use Sonder\Plugins\Router\RouterException;
use Sonder\Plugins\RouterPlugin;

#[IMiddleware]
#[IRouterMiddleware]
final class RouterMiddleware extends CoreMiddleware implements IRouterMiddleware
{
    private const DEFAULT_ROUTING_TYPE = 'default';

    private const DEFAULT_CONTROLLER = 'main';

    private const DEFAULT_CONTROLLER_METHOD = 'displayNotFound';

    private const ANNOTATIONS_ROUTING_TYPE = 'annotations';

    /**
     * @return void
     * @throws AnnotationException
     * @throws CoreException
     * @throws MiddlewareException
     * @throws RouterException
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
                empty($this->request->getControllerMethod())
            ) &&
            APP_ROUTING_TYPE == 'annotations'
        ) {
            $this->_setRouteByAnnotations();
        }

        if (
            (
                empty($this->request->getController()) ||
                empty($this->request->getControllerMethod())
            ) &&
            APP_ROUTING_TYPE == 'default'
        ) {
            $this->_setRouteByUrlParams();
        }
    }

    /**
     * @return void
     * @throws AnnotationException
     * @throws CoreException
     * @throws RouterException
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

        /* @var RouterPlugin $routerPlugin */
        $routerPlugin = $this->getPlugin('router', $controllersPaths);

        $routerPlugin->cleanCache();

        $controller = RouterMiddleware::DEFAULT_CONTROLLER;
        $controllerMethod = RouterMiddleware::DEFAULT_CONTROLLER_METHOD;

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

            $controllerMethod = $route->getControllerMethod();

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
        $this->request->setControllerMethod($controllerMethod);
    }

    /**
     * @return void
     */
    private function _setRouteByUrlParams(): void
    {
        $controller = $this->request->getUrlValue('controller');
        $controllerMethod = $this->request->getUrlValue('method');

        if (!empty($controllerMethod)) {
            $controllerMethod = sprintf(
                'display%s',
                mb_convert_case($controllerMethod, MB_CASE_TITLE)
            );
        }

        if (empty($controller)) {
            $controller = RouterMiddleware::DEFAULT_CONTROLLER;
        }

        if (empty($controllerMethod)) {
            $controllerMethod = RouterMiddleware::DEFAULT_CONTROLLER_METHOD;
        }

        $this->request->setController($controller);
        $this->request->setControllerMethod($controllerMethod);
    }
}
