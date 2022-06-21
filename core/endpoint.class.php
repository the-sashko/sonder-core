<?php

namespace Sonder\Core;

use ReflectionMethod;
use Sonder\Enums\CacheTypesEnum;
use Sonder\Enums\EventTypesEnum;
use Sonder\Enums\MiddlewaresEnum;
use Sonder\Exceptions\CacheException;
use Sonder\Exceptions\ConfigException;
use Sonder\Exceptions\RequestObjectException;
use Sonder\Interfaces\ICacheObject;
use Sonder\Interfaces\IController;
use Sonder\Interfaces\IEndpoint;
use Sonder\Interfaces\IMiddleware;
use Sonder\Exceptions\AppException;
use Sonder\Exceptions\EndpointException;
use Sonder\Interfaces\IRequestObject;
use Sonder\Interfaces\IResponseObject;

#[IEndpoint]
class CoreEndpoint implements IEndpoint
{
    protected const CACHE_TTL = 1800; // 30 min

    /**
     * @var array
     */
    protected array $middlewares;

    /**
     * @var IRequestObject
     */
    #[IRequestObJect]
    private IRequestObJect $_request;

    /**
     * @var IResponseObject|null
     */
    #[IResponseObJect]
    private ?IResponseObJect $_response;

    /**
     * @var ICacheObject
     */
    #[ICacheObject]
    private ICacheObject $_cache;

    /**
     * @throws RequestObjectException
     */
    public function __construct()
    {
        $this->_cache = new CacheObject(CacheTypesEnum::APP);

        $this->_request = new RequestObject();

        $this->_response = $this->_getResponseFromCache();

        if (empty($this->_response)) {
            $this->_init();
        }
    }

    /**
     * @return void
     * @throws CacheException
     * @throws ConfigException
     * @throws EndpointException
     * @throws RequestObjectException
     */
    final public function run(): void
    {
        $this->_runEvent(EventTypesEnum::BEFORE_MIDDLEWARES);

        if (!empty($this->_response)) {
            $this->_returnResponse();
        }

        $this->_runMiddlewares();

        $this->_runEvent(EventTypesEnum::AFTER_MIDDLEWARES);

        if (!empty($this->_response)) {
            $this->_returnResponse();
        }

        $this->_runControllerMethod();
        $this->_saveResponseToCache();

        if (!empty($this->_response)) {
            $this->_returnResponse();
        }

        throw new EndpointException(
            EndpointException::MESSAGE_ENDPOINT_RESPONSE_IS_EMPTY,
            AppException::CODE_ENDPOINT_RESPONSE_IS_EMPTY
        );
    }

    /**
     * @return never
     */
    private function _returnResponse(): never
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
     * @return void
     */
    private function _runMiddlewares(): void
    {
        foreach ($this->middlewares as $middleware) {
            if (!is_string($middleware) && !empty($middleware)) {
                $middleware = $middleware->value;
            }

            $middleware = $this->_getMiddlewareInstance($middleware);
            $middleware->run();

            $response = $middleware->getResponse();

            if (!empty($response)) {
                $this->_response = $response;
            }
        }
    }

    /**
     * @return void
     * @throws EndpointException
     */
    private function _runControllerMethod(): void
    {
        $controller = $this->_getControllerInstance();

        if (!$this->_isValidControllerMethod($controller)) {
            throw new EndpointException(
                EndpointException::MESSAGE_ENDPOINT_INVALID_CONTROLLER_METHOD,
                AppException::CODE_ENDPOINT_INVALID_CONTROLLER_METHOD
            );
        }

        $method = $this->_request->getControllerMethod();

        $this->_response = $controller->$method();
    }

    /**
     * @param string|null $middleware
     * @return IMiddleware
     */
    private function _getMiddlewareInstance(
        ?string $middleware = null
    ): IMiddleware {
        $middleware = sprintf(
            '\Sonder\Middlewares\%sMiddleware',
            mb_convert_case($middleware, MB_CASE_TITLE)
        );

        return new $middleware($this->_request);
    }

    /**
     * @return IController
     * @throws EndpointException
     */
    private function _getControllerInstance(): IController
    {
        $controller = $this->_request->getController();

        if (empty($controller)) {
            throw new EndpointException(
                EndpointException::MESSAGE_ENDPOINT_CONTROLLER_IS_NOT_SET,
                AppException::CODE_ENDPOINT_CONTROLLER_IS_NOT_SET
            );
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
        $method = $this->_request->getControllerMethod();

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

        return $returnType == 'IResponseObject';
    }

    /**
     * @return void
     */
    private function _init(): void
    {
        if (defined('APP_MIDDLEWARES')) {
            $this->middlewares = array_merge(
                $this->middlewares,
                APP_MIDDLEWARES
            );
        }

        if (empty($this->middlewares)) {
            $this->middlewares = MiddlewaresEnum::DEFAULT_MIDDLEWARES;
        }

        $this->middlewares = array_merge(
            [
                MiddlewaresEnum::SECURITY
            ],
            $this->middlewares,
            [
                MiddlewaresEnum::ROUTER
            ]
        );
    }

    /**
     * @return ResponseObject|null
     * @throws RequestObjectException
     */
    private function _getResponseFromCache(): ?ResponseObject
    {
        if (
            !$this->_request->getHttpMethod()->isGet() ||
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
     * @return void
     * @throws RequestObjectException
     * @throws CacheException
     */
    private function _saveResponseToCache(): void
    {
        if (
            $this->_request->getHttpMethod()->isGet() &&
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
     * @throws RequestObjectException
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

    /**
     * @param EventTypesEnum $eventType
     * @return void
     * @throws ConfigException
     */
    private function _runEvent(EventTypesEnum $eventType): void
    {
        $values = (new CoreEvent)->run(
            $eventType,
            [
                'request' => $this->_request,
                'response' => $this->_response
            ]
        );

        $this->_request = $values['request'] ?? null;
        $this->_response = $values['response'] ?? null;
    }
}
