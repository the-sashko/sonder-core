<?php

namespace SonderCore\Core;

use Exception;
use ReflectionMethod;

use SonderCore\Core\Interfaces\IController;
use SonderCore\Core\Interfaces\IEndpoint;
use SonderCore\Core\Interfaces\IMiddleware;

class CoreEndpoint implements IEndpoint
{
    const DEFAULT_MIDDLEWARES = [
        'security',
        'session',
        'router'
    ];

    /**
     * @var array
     */
    private array $_middlewares;

    /**
     * @var RequestObject
     */
    private RequestObJect $_request;

    /**
     * @var ResponseObject
     */
    private ResponseObJect $_response;

    public function __construct()
    {
        $this->_middlewares = static::DEFAULT_MIDDLEWARES;

        if (defined('APP_MIDDLEWARES')) {
            $this->_middlewares = APP_MIDDLEWARES;
        }

        $this->_request = new RequestObject();
        $this->_response = new ResponseObject();
    }

    /**
     * @throws Exception
     */
    final public function run(): void
    {
        foreach ($this->_middlewares as $middleware) {
            $middleware = $this->_getMiddlewareInstance($middleware);

            $this->_request = $middleware->getRequest();
        }

        $controller = $this->_getControllerInstance();

        $method = $this->_request->getRoute()->getMethod();

        if (!$this->_isValidControllerMethod($controller)) {
            throw new Exception('Invalid Controller Method');
        }

        $this->_response = (new $controller)->$method();

        $this->_response->setHttpHeader();

        echo $this->_response->getContent();

        exit(0);
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
            '\SonderCore\Middlewares\%sMiddleware',
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
        $controller = $this->_request->getRoute()->getController();

        if (empty($controller)) {
            throw new Exception('Controller Is Not set');
        }

        $controller = sprintf(
            '\SonderCore\Controllers\%sController',
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
        $method = $this->_request->getRoute()->getMethod();

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
}
