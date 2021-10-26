<?php

namespace Sonder\Plugins\Router\Classes;

use Sonder\Plugins\Router\Exceptions\RouterEntityException;
use Sonder\Plugins\Router\Exceptions\RouterException;
use Sonder\Plugins\Router\Interfaces\IRouterEntity;

final class RouterEntity implements IRouterEntity
{
    const DEFAULT_AREA = 'default';

    /**
     * @var string
     */
    private string $_area;

    /**
     * @var string
     */
    private string $_route;

    /**
     * @var string
     */
    private string $_controllerFilePath;

    /**
     * @var string
     */
    private string $_controller;

    /**
     * @var string
     */
    private string $_method;

    /**
     * @var string|null
     */
    private ?string $_params;

    /**
     * @var string|null
     */
    private ?string $_language = null;

    /**
     * @var bool
     */
    private bool $_noCache;

    /**
     * @param string|null $area
     * @param string|null $route
     * @param string|null $params
     * @param string|null $controller
     * @param string|null $controllerFilePath
     * @param string|null $method
     * @param bool $noCache
     */
    final public function __construct(
        ?string $area = null,
        ?string $route = null,
        ?string $params = null,
        ?string $controller = null,
        ?string $controllerFilePath = null,
        ?string $method = null,
        bool    $noCache = false
    )
    {
        $this->_area = $area;
        $this->_route = $route;
        $this->_controllerFilePath = $controllerFilePath;
        $this->_controller = $controller;
        $this->_method = $method;
        $this->_params = $params;
        $this->_noCache = $noCache;
    }

    /**
     * @return array
     */
    final public function __serialize(): array
    {
        return [
            'area' => $this->_area,
            'route' => base64_encode($this->_route),
            'controller_file_path' => base64_encode($this->_controllerFilePath),
            'controller' => $this->_controller,
            'method' => $this->_method,
            'params' => base64_encode($this->_params),
            'language' => $this->_language,
            'no_cache' => (int)$this->_noCache
        ];
    }

    /**
     * @param array $values
     */
    final public function __unserialize(array $values): void
    {
        $this->_area = $values['area'];

        $this->_route = base64_decode($values['route']);

        $this->_controllerFilePath = base64_decode(
            $values['controller_file_path']
        );

        $this->_controller = $values['controller'];

        $this->_method = $values['method'];

        $this->_params = base64_decode($values['params']);

        $this->_language = empty($values['language']) ? null : $this->_language;

        $this->_noCache = (bool)$values['no_cache'];
    }

    /**
     * @return string
     */
    final public function getArea(): string
    {
        if (empty($this->_area)) {
            return $this->_getDefaultArea();
        }

        return $this->_area;
    }

    /**
     * @return string
     *
     * @throws RouterEntityException
     */
    final public function getRoute(): string
    {
        if (empty($this->_route)) {
            throw new RouterEntityException(
                RouterEntityException::MESSAGE_ENTITY_ROUTE_IS_NOT_SET,
                RouterException::CODE_ENTITY_ROUTE_IS_NOT_SET
            );
        }

        return $this->_route;
    }

    /**
     * @return string
     *
     * @throws RouterEntityException
     */
    final public function getRoutePattern(): string
    {
        $route = $this->getRoute();
        $route = str_replace('/', '\/', $route);

        return sprintf('/^%s$/su', $route);
    }

    /**
     * @return string
     *
     * @throws RouterEntityException
     */
    final public function getControllerFilePath(): string
    {
        if (empty($this->_controller)) {
            throw new RouterEntityException(
                RouterEntityException::MESSAGE_ENTITY_FILE_PATH_IS_NOT_SET,
                RouterException::CODE_ENTITY_FILE_PATH_IS_NOT_SET
            );
        }

        return $this->_controllerFilePath;
    }

    /**
     * @return string
     *
     * @throws RouterEntityException
     */
    final public function getController(): string
    {
        if (empty($this->_controller)) {
            throw new RouterEntityException(
                RouterEntityException::MESSAGE_ENTITY_CONTROLLER_IS_NOT_SET,
                RouterException::CODE_ENTITY_CONTROLLER_IS_NOT_SET
            );
        }

        return $this->_controller;
    }

    /**
     * @return string
     *
     * @throws RouterEntityException
     */
    final public function getMethod(): string
    {
        if (empty($this->_method)) {
            throw new RouterEntityException(
                RouterEntityException::MESSAGE_ENTITY_METHOD_IS_NOT_SET,
                RouterException::CODE_ENTITY_METHOD_IS_NOT_SET
            );
        }

        return $this->_method;
    }

    /**
     * @return array|null
     *
     * @throws RouterEntityException
     */
    final public function getParams(): ?array
    {
        if (empty($this->_params)) {
            return null;
        }

        $routePattern = $this->getRoutePattern();

        $params = $this->_params;

        $url = $_SERVER['REQUEST_URI'];
        $url = explode('#', $url);
        $url = array_shift($url);
        $url = explode('&', $url);
        $url = array_shift($url);
        $url = explode('?', $url);
        $url = array_shift($url);

        if (!preg_match($routePattern, $url)) {
            return null;
        }

        $params = preg_replace($routePattern, $params, $url);

        parse_str($params, $params);

        return $params;
    }

    /**
     * @return string|null
     */
    final public function getLanguage(): ?string
    {
        return $this->_language;
    }

    /**
     * @return bool
     */
    final public function getNoCache(): bool
    {
        return $this->_noCache;
    }

    /**
     * @param array|null $params
     */
    final public function setParams(?array $params = null): void
    {
        $params = (string)$this->_params;

        parse_str($params, $params);

        $params = array_merge((array)$params, (array)$params);
        $params = http_build_query($params);
        $params = urldecode($params);

        $this->_params = !empty($params) ? $params : null;
    }

    /**
     * @param string|null $language
     */
    final public function setLanguage(?string $language = null): void
    {
        $this->_language = $language;
    }

    /**
     * @return string
     */
    private function _getDefaultArea(): string
    {
        $defaultArea = RouterEntity::DEFAULT_AREA;

        if (defined('APP_AREA')) {
            $defaultArea = APP_MODE;
        }

        return $defaultArea;
    }
}
