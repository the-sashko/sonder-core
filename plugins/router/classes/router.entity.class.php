<?php
namespace Core\Plugins\Router\Classes;

use Core\Plugins\Router\Interfaces\IRouterEntity;

use Core\Plugins\Router\Exceptions\RouterEntityException;

class RouterEntity implements IRouterEntity
{
    const DEFAULT_AREA = 'default';

    private $_area = null;

    private $_route = null;

    private $_params = null;

    private $_controller = null;

    private $_method = null;

    private $_language = null;

    private $_page = 1;

    public function __construct(
        ?string $area       = null,
        ?string $route      = null,
        ?string $params     = null,
        ?string $controller = null,
        ?string $method     = null
    )
    {
        $this->_area       = $area;
        $this->_route      = $route;
        $this->_controller = $controller;
        $this->_method     = $method;
        $this->_params     = $params;

        if (!empty($this->_params)) {
            $this->_params = preg_replace(
                $this->getRoutePattern(),
                $this->_params,
                $_SERVER['REQUEST_URI']
            );

            parse_str($this->_params, $this->_params);   
        }
    }

    public function getArea(): string
    {
        if (empty($this->_area)) {
            return $this->_getDefaultArea();
        }

        return $this->_area;
    }

    public function getRoute(): string
    {
        if (empty($this->_route)) {
            throw new RouterEntityException(
                RouterEntityException::MESSAGE_ENTITY_ROUTE_IS_NOT_SET,
                RouterEntityException::CODE_ENTITY_ROUTE_IS_NOT_SET
            );
        }

        return $this->_route;
    }

    public function getRoutePattern(): string
    {
        $route = $this->getRoute();
        $route = str_replace('/', '\/', $route);

        return sprintf('/^%s$/su', $route);
    }

    public function getParams(): ?array
    {
        if (empty($this->_params)) {
            return null;
        }

        return $this->_params;
    }

    public function getController(): string
    {
        if (empty($this->_controller)) {
            throw new RouterEntityException(
                RouterEntityException::MESSAGE_ENTITY_CONTROLLER_IS_NOT_SET,
                RouterEntityException::CODE_ENTITY_CONTROLLER_IS_NOT_SET
            );
        }

        return $this->_controller;
    }

    public function getMethod(): string
    {
        if (empty($this->_method)) {
            throw new RouterEntityException(
                RouterEntityException::MESSAGE_ENTITY_METHOD_IS_NOT_SET,
                RouterEntityException::CODE_ENTITY_METHOD_IS_NOT_SET
            );
        }

        return $this->_method;
    }

    public function getPage(): int
    {
        if (empty($this->_page)) {
            return 1;
        }

        return (int) $this->_page;
    }

    public function getLanguage(): ?string
    {
        return $this->_language;
    }

    public function setPage(?int $page = null): void
    {
        $this->_page = !empty($page) ? (int) $page : null;
    }

    public function setLanguage(?string $language = null): void
    {
        $this->_language = $language;
    }

    public function setParams(?array $params = null): void
    {
        $this->_params = array_merge((array) $this->_params, (array) $params);
        $this->_params = !empty($this->_params) ? $this->_params : null;
    }

    private function _getDefaultArea(): string
    {
        $defaultArea = static::DEFAULT_AREA;

        if (defined('APP_AREA')) {
            $defaultArea = APP_MODE;
        }

        return $defaultArea;
    }
}

