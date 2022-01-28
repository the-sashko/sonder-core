<?php

namespace Sonder\Core;

use Exception;
use ReflectionMethod;
use Sonder\Core\Interfaces\IController;
use Sonder\Core\Interfaces\IEndpoint;
use Sonder\Core\Interfaces\IMiddleware;

class CoreEndpoint implements IEndpoint
{
    const CACHE_TTL = 60 * 30;

    const DEFAULT_MIDDLEWARES = [
        'session'
    ];

    const SECURITY_MIDDLEWARE = 'security';

    const ROUTER_MIDDLEWARE = 'router';

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

    private CacheObject $_cache;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->_cache = new CacheObject('app');

        $this->_request = new RequestObject();
        $this->_response = $this->_getResponseFromCache();

        if (empty($this->_response)) {
            $this->_init();
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function run(): void
    {
        $values = (new CoreEvent)->run(
            CoreEvent::TYPE_BEFORE_MIDDLEWARES,
            [
                'request' => $this->_request,
                'response' => $this->_response
            ]
        );

        $this->_request = $values['request'];
        $this->_response = $values['response'];

        if (!empty($this->_response)) {
            $this->_returnResponse();
        }

        $this->_runMiddlewares();

        $values = (new CoreEvent)->run(
            CoreEvent::TYPE_AFTER_MIDDLEWARES,
            [
                'request' => $this->_request,
                'response' => $this->_response
            ]
        );

        $this->_request = $values['request'];
        $this->_response = $values['response'];

        if (!empty($this->_response)) {
            $this->_returnResponse();
        }

        $this->_runControllerMethod();
        $this->_saveResponseToCache();

        if (!empty($this->_response)) {
            $this->_returnResponse();
        }

        throw new Exception('Response Is Empty');
    }

    /**
     * @return void
     */
    private function _returnResponse(): void
    {
        $this->_response->redirect->redirect();

        if (!headers_sent()) {
            header($this->_response->getContentTypeHeader());
        }

        http_response_code($this->_response->getHttpCode());

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

            $response = $middleware->getResponse();

            if (!empty($response)) {
                $this->_response = $response;
            }
        }
    }

    /**
     * @throws Exception
     */
    private function _runControllerMethod(): void
    {
        $controller = $this->_getControllerInstance();

        if (!$this->_isValidControllerMethod($controller)) {
            throw new Exception('Invalid Controller Method');
        }

        $method = $this->_request->getMethod();

        $this->_response = $controller->$method();
    }

    /**
     * @param string|null $middleware
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

        $reflectionReturnType = $reflection->getReturnType();

        if (empty($reflectionReturnType)) {
            return false;
        }

        if ($reflectionReturnType->allowsNull()) {
            return false;
        }

        $returnType = $reflectionReturnType->getName();
        $returnType = explode('\\', $returnType);
        $returnType = end($returnType);

        return $returnType == 'ResponseObject';
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

    /**
     * @return ResponseObject|null
     * @throws Exception
     */
    private function _getResponseFromCache(): ?ResponseObject
    {
        if (
            $this->_request->getHttpMethod() != 'get' ||
            $this->_request->getNoCache()
        ) {
            return null;
        }

        $cacheValues = $this->_cache->get($this->_getCacheIdent());

        if (
            empty($cacheValues) ||
            !array_key_exists('response', $cacheValues)
        ) {
            return null;
        }

        return unserialize(base64_decode($cacheValues['response']));
    }

    /**
     * @throws Exception
     */
    private function _saveResponseToCache(): void
    {
        if (
            $this->_request->getHttpMethod() == 'get' &&
            !$this->_request->getNoCache()
        ) {
            $this->_cache->save(
                $this->_getCacheIdent(),
                [
                    'response' => base64_encode(serialize($this->_response))
                ],
                static::CACHE_TTL
            );
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    private function _getCacheIdent(): string
    {
        $userId = 0;

        if (!empty($this->_request->getUser())) {
            $userId = (int)$this->_request->getUser()->getId();
        }

        return sprintf(
            '%d_%s',
            $userId,
            base64_encode($this->_request->getFullUrl())
        );
    }
}
