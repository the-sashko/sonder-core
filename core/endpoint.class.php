<?php

namespace SonderCore\Core;

use SonderCore\Core\RequestObject;
use SonderCore\Core\ResponseObject;

use SonderCore\Core\Interfaces\IEndpoint;

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

    private RequestObJect $_request;

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

    final public function run(): void
    {
        foreach ($this->_middlewares as $middleware) {
            $this->_request = $this->_runMiddleware($middleware);
        }

        $controller = $this->_request->getRoute()->getController();
        $method = $this->_request->getRoute()->getMethod();

        $this->_response = (new $controller)->$method();

        //ToDo
    }

    /**
     * @param string|null $middleware
     *
     * @return RequestObject
     */
    private function _runMiddleware(?string $middleware = null): RequestObject
    {
        $middleware = sprintf(
            '\SonderCore\Middlewares\%sMiddleware',
            mb_convert_case($middleware, MB_CASE_TITLE)
        );

        $middleware = new $middleware($this->_request);

        return $middleware->getRequest();
    }
}
