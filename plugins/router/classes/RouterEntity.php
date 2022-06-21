<?php

namespace Sonder\Plugins\Router\Classes;

use Sonder\Plugins\Router\Interfaces\IRouterEntity;

#[IRouterEntity]
final class RouterEntity implements IRouterEntity
{
    final public const DEFAULT_AREA = 'default';

    /**
     * @var string|null
     */
    private ?string $_language = null;

    /**
     * @param string $_area
     * @param string $_route
     * @param string|null $_params
     * @param bool $_noCache
     * @param string $_controller
     * @param string $_controllerMethod
     * @param string $_controllerFilePath
     */
    final public function __construct(
        private string $_area,
        private string $_route,
        private ?string $_params,
        private bool $_noCache,
        private string $_controller,
        private string $_controllerMethod,
        private string $_controllerFilePath,
    ) {
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
            'controller_method' => $this->_controllerMethod,
            'params' => base64_encode((string)$this->_params),
            'language' => $this->_language,
            'no_cache' => (int)$this->_noCache
        ];
    }

    /**
     * @param array $values
     * @return void
     */
    final public function __unserialize(array $values): void
    {
        $this->_area = $values['area'];

        $this->_route = base64_decode($values['route']);

        $this->_controllerFilePath = base64_decode(
            $values['controller_file_path']
        );

        $this->_controller = $values['controller'];

        $this->_controllerMethod = $values['method'];

        $this->_params = base64_decode($values['params']);
        $this->_params = empty($this->_params) ? null : $this->_params;

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
     */
    final public function getRoute(): string
    {
        return $this->_route;
    }

    /**
     * @return string
     */
    final public function getRoutePattern(): string
    {
        $route = $this->getRoute();
        $route = str_replace('/', '\/', $route);

        return sprintf('/^%s$/su', $route);
    }

    /**
     * @return array|null
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
     * @return bool
     */
    final public function getNoCache(): bool
    {
        return $this->_noCache;
    }

    /**
     * @return string
     */
    final public function getControllerFilePath(): string
    {
        return $this->_controllerFilePath;
    }

    /**
     * @return string
     */
    final public function getController(): string
    {
        return $this->_controller;
    }

    /**
     * @return string
     */
    final public function getControllerMethod(): string
    {
        return $this->_controllerMethod;
    }

    /**
     * @return string|null
     */
    final public function getLanguage(): ?string
    {
        return $this->_language;
    }

    /**
     * @param string|null $language
     * @return void
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
