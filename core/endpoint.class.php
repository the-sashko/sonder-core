<?php

namespace Sonder\Core;

use Exception;
use ReflectionMethod;
use Sonder\Core\Interfaces\IController;
use Sonder\Core\Interfaces\IEndpoint;
use Sonder\Core\Interfaces\IMiddleware;

class CoreEndpoint implements IEndpoint
{
    const SECURITY_MIDDLEWARE = 'security';

    const ROUTER_MIDDLEWARE = 'router';

    const DEFAULT_MIDDLEWARES = [
        'session'
    ];

    /**
     * @var array
     */
    protected array $middlewares;

    /**
     * @var RequestObject
     */
    private RequestObJect $_request;

    /**
     * @var ResponseObject|null
     */
    private ?ResponseObJect $_response;

    public function __construct()
    {
        $this->_request = new RequestObject();
        $this->_response = $this->_getResponseFromCache();

        if (empty($this->_response)) {
            $this->_init();
        }
    }

    /**
     * @throws Exception
     */
    final public function run(): void
    {
        if (empty($this->_response)) {
            $this->_runMiddlewares();
        }

        $this->_saveResponseToCache();

        $this->_response->setHttpHeader();

        echo $this->_response->getContent();

        exit(0);
    }

    /**
     * @throws Exception
     */
    private function _runMiddlewares(): void
    {
        foreach ($this->middlewares as $middleware) {
            $middleware = $this->_getMiddlewareInstance($middleware);
            $middleware->run();
        }

        $controller = $this->_getControllerInstance();

        $method = $this->_request->getMethod();

        if (!$this->_isValidControllerMethod($controller)) {
            throw new Exception('Invalid Controller Method');
        }

        $this->_response = $controller->$method();
    }

    /**
     * @param string|null $middleware
     *
     * @return IMiddleware
     */
    private function _getMiddlewareInstance(
        ?string $middleware = null
    ): IMiddleware
    {
        $middleware = sprintf(
            '\Sonder\Middlewares\%sMiddleware',
            mb_convert_case($middleware, MB_CASE_TITLE)
        );

        return new $middleware($this->_request);
    }

    /**
     * @return IController
     *
     * @throws Exception
     */
    private function _getControllerInstance(): IController
    {
        $controller = $this->_request->getController();

        if (empty($controller)) {
            throw new Exception('Controller Is Not set');
        }

        $controller = sprintf(
            '\Sonder\Controllers\%sController',
            mb_convert_case($controller, MB_CASE_TITLE)
        );

        return new $controller($this->_request);
    }

    /**
     * @param IController $controller
     *
     * @return bool
     */
    private function _isValidControllerMethod(IController $controller): bool
    {
        $method = $this->_request->getMethod();

        if (empty($method)) {
            return false;
        }

        if (!method_exists($controller, $method)) {
            return false;
        }

        $reflection = new ReflectionMethod($controller, $method);

        if (!$reflection->isPublic()) {
            return false;
        }

        return true;
    }

    private function _init(): void
    {
        if (defined('APP_MIDDLEWARES')) {
            $this->middlewares = array_merge(
                $this->middlewares,
                APP_MIDDLEWARES
            );
        }

        if (empty($this->middlewares)) {
            $this->middlewares = static::DEFAULT_MIDDLEWARES;
        }

        $this->middlewares = array_merge(
            [
                static::SECURITY_MIDDLEWARE
            ],
            $this->middlewares,
            [
                static::ROUTER_MIDDLEWARE
            ]
        );
    }

    private function _getResponseFromCache(): ?RequestObject
    {
        //TODO

        return null;
    }

    private function _saveResponseToCache(): void
    {
        //TODO
    }
}
