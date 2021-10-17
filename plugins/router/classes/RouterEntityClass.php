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
    private string $_params;

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
    private ?string $_language = null;

    /**
     * @var int|null
     */
    private ?int $_page = null;

    /**
     * @param string|null $area
     * @param string|null $route
     * @param string|null $params
     * @param string|null $controller
     * @param string|null $method
     */
    final public function __construct(
        ?string $area = null,
        ?string $route = null,
        ?string $params = null,
        ?string $controller = null,
        ?string $method = null
    )
    {
        $this->_area = $area;
        $this->_route = $route;
        $this->_controller = $controller;
        $this->_method = $method;
        $this->_params = $params;
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

        if (preg_match('/^(.*?)\/page-([0-9]+)\//su', $url)) {
            $url = preg_replace('/^(.*?)\/page-([0-9]+)\//su', '$1/', $url);
        }

        if (!preg_match($routePattern, $url)) {
            return null;
        }

        $params = preg_replace($routePattern, $params, $url);

        parse_str($params, $params);

        return $params;
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
     * @return int
     */
    final public function getPage(): int
    {
        if (empty($this->_page)) {
            return 1;
        }

        return $this->_page;
    }

    /**
     * @return string|null
     */
    final public function getLanguage(): ?string
    {
        return $this->_language;
    }

    /**
     * @param int|null $page
     */
    final public function setPage(?int $page = null): void
    {
        $this->_page = !empty($page) ? $page : null;
    }

    /**
     * @param string|null $language
     */
    final public function setLanguage(?string $language = null): void
    {
        $this->_language = $language;
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
