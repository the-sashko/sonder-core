<?php
use Core\Plugins\Router\Interfaces\IRouterPlugin;
use Core\Plugins\Router\Interfaces\IRouterEntity;

use Core\Plugins\Router\Classes\RouterEntity;

use Core\Plugins\Router\Exceptions\RouterPluginException;

class RouterPlugin implements IRouterPlugin
{
    const CONTROLLERS_FILE_PATTERN = __DIR__.'/../../../'.
                                    'controllers/*Controller.php';

    private $_annotationPlugin = null;

    private $_cache = null;

    private $_language = null;

    private $_page = null;

    public function __construct()
    {
        $this->_annotationPlugin = new AnnotationPlugin();

        $this->_cache = new RouterCache();
    }

    public function getRoute(?string $url = null): ?IRouterEntity
    {
        if (empty($url)) {
            throw new RouterPluginException(
                RouterPluginException::MESSAGE_PLUGIN_URL_IS_NOT_SET,
                RouterPluginException::CODE_PLUGIN_URL_IS_NOT_SET
            );
        }

        if (empty($this->_cache)) {
            throw new RouterPluginException(
                RouterPluginException::MESSAGE_PLUGIN_CACHE_IS_NOT_SET,
                RouterPluginException::CODE_PLUGIN_CACHE_IS_NOT_SET
            );
        }

        $url = $this->_extractLanguageFromUrl($url);
        $url = $this->_extractPageFromUrl($url);

        $route = $this->_cache->getRouteByUrl($url);

        if (empty($route)) {
            $route = $this->_getRouteByUrl($url);
        }

        if (!empty($route)) {
            $route->setPage($this->_page);
            $route->setLanguage($this->_language);

            $this->_cache->saveRouteUrl($url, $route);
        }

        return $route;
    }

    public function cleanCache(): void
    {
        if (empty($this->_cache)) {
            throw new RouterPluginException(
                RouterPluginException::MESSAGE_PLUGIN_CACHE_IS_NOT_SET,
                RouterPluginException::CODE_PLUGIN_CACHE_IS_NOT_SET
            );
        }

        $this->_cache->clean();
    }

    private function _extractLanguageFromUrl(string $url): string
    {
        if (!defined('APP_MULTI_LANGUAGE') || !APP_MULTI_LANGUAGE) {
            return $url;
        }

        if (preg_match('/^\/([a-z]{2})\/(.*?)$/su', $url)) {
            $this->_language = preg_replace(
                '/^\/([a-z]{2})\/(.*?)$/su',
                '$1',
                $url
            );

            $url = preg_replace('/^\/([a-z]{2})\/(.*?)$/su', '$2', $url);
        }

        $url = !empty($url) ? $url : '/';

        return $url;
    }

    private function _extractPageFromUrl(string $url): string
    {
        if (preg_match('/^(.*?)\/page-([0-9]+)\/$/su', $url)) {
            $this->_page = (int) preg_replace(
                '/^(.*?)\/page-([0-9]+)\/$/su',
                '$2',
                $url
            );

            $url = preg_replace('/^(.*?)\/page-([0-9]+)\/$/su', '$2', $url);
        }

        $url = !empty($url) ? $url : '/';

        return $url;
    }

    private function _getRouteByUrl(string $url): ?IRouterEntity
    {
        $routeByUrl = null;

        if (empty($url)) {
            throw new RouterPluginException(
                RouterPluginException::MESSAGE_PLUGIN_URL_IS_NOT_SET,
                RouterPluginException::CODE_PLUGIN_URL_IS_NOT_SET
            );
        }

        if (empty($this->_cache)) {
            throw new RouterPluginException(
                RouterPluginException::MESSAGE_PLUGIN_CACHE_IS_NOT_SET,
                RouterPluginException::CODE_PLUGIN_CACHE_IS_NOT_SET
            );
        }

        $routes = $this->_cache->getRoutes();

        if (empty($routes)) {
            $routes = $this->_getRoutesFromAnnotations();
            $routes = iterator_to_array($routes);

            $this->_cache->saveRoutes($routes);
        }

        foreach ($routes as $route) {
            if (preg_match($route->getRoutePattern(), $url)) {
                $routeByUrl = $route;
            }
        }

        return $routeByUrl;
    }

    private function _getRoutesFromAnnotations(): \Generator
    {
        $routes = [];

        foreach ($this->_getControllersDisplayMethods() as $methods) {
            $routes = array_merge(
                $routes,
                iterator_to_array($this->_getRoutesFromClassMethods($methods))
            );
        }

        foreach ($routes as $route) {
            yield $route;
        }

        return null;
    }

    private function _getRoutesFromClassMethods(
        ?array $methods = null
    ): \Generator
    {
        $controllerClass = $methods['controller'];
        $methods         = $methods['methods'];

        foreach ($methods as $method) {
            $routeArea   = $this->_getRouteArea($controllerClass, $method);
            $routePath   = $this->_getRoutePath($controllerClass, $method);
            $routeParams = $this->_getRouteParams($controllerClass, $method);

            if (empty($routePath)) {
                continue;
            }

            yield new RouterEntity(
                $routeArea,
                $routePath,
                $routeParams,
                $controllerClass,
                $method
            );
        }

        return null;
    }

    private function _getControllersDisplayMethods(): \Generator
    {
        foreach (glob(static::CONTROLLERS_FILE_PATTERN) as $controllerFile) {
            $controllerClass = preg_replace(
                '/^(.*?)\/([A-z]+)\.php$/su',
                '$2',
                $controllerFile
            );

            require_once $controllerFile;

            $methods = $this->_getDisplayMethodsByClassName($controllerClass);

            yield [
                'controller' => $controllerClass,
                'methods'    => $methods
            ];
        }
    }

    private function _getDisplayMethodsByClassName(
        ?string $className = null
    ): \Generator
    {
        if (empty($className)) {
            throw new RouterPluginException(
                RouterPluginException::MESSAGE_PLUGIN_CONTROLLER_IS_NOT_SET,
                RouterPluginException::CODE_PLUGIN_CONTROLLER_IS_NOT_SET
            );
        }

        $reflection = new ReflectionClass($className);

        $methods = $reflection->getMethods();

        foreach ($methods as $method) {
            if (!$method->isPublic()) {
                continue;
            }

            $method = $method->name;

            if (preg_match('/^display(.*?)$/su', $method)) {
                yield $method;
            }
        }
    }

    private function _getRouteArea(
        ?string $className  = null,
        ?string $methodName = null
    ): string
    {
        if (empty($className)) {
            throw new RouterPluginException(
                RouterPluginException::MESSAGE_PLUGIN_CONTROLLER_IS_NOT_SET,
                RouterPluginException::CODE_PLUGIN_CONTROLLER_IS_NOT_SET
            );
        }

        if (empty($this->_annotationPlugin)) {
            throw new RouterPluginException(
                RouterPluginException::MESSAGE_PLUGIN_ANNOTATION_IS_NOT_SET,
                RouterPluginException::CODE_PLUGIN_ANNOTATION_IS_NOT_SET
            );
        }

        $routeArea = $this->_annotationPlugin->getAnnotation(
            $className,
            $methodName,
            'area'
        );

        $routeArea = mb_convert_case($routeArea, MB_CASE_LOWER);
        $routeArea = preg_replace('/([^a-z]+)/su', '', $routeArea);

        return !empty($routeArea) ? $routeArea : RouterEntity::DEFAULT_AREA;
    }

    private function _getRoutePath(
        ?string $className  = null,
        ?string $methodName = null
    ): ?string
    {
        if (empty($className)) {
            throw new RouterPluginException(
                RouterPluginException::MESSAGE_PLUGIN_CONTROLLER_IS_NOT_SET,
                RouterPluginException::CODE_PLUGIN_CONTROLLER_IS_NOT_SET
            );
        }

        if (empty($this->_annotationPlugin)) {
            throw new RouterPluginException(
                RouterPluginException::MESSAGE_PLUGIN_ANNOTATION_IS_NOT_SET,
                RouterPluginException::CODE_PLUGIN_ANNOTATION_IS_NOT_SET
            );
        }

        $routePath = $this->_annotationPlugin->getAnnotation(
            $className,
            $methodName,
            'route'
        );

        $routePath = preg_replace('/\s+/su', '', $routePath);
        $routePath = !empty($routePath) ? $routePath : null;

        return $routePath;
    }

    private function _getRouteParams(
        ?string $className  = null,
        ?string $methodName = null
    ): ?string
    {
        if (empty($className)) {
            throw new RouterPluginException(
                RouterPluginException::MESSAGE_PLUGIN_CONTROLLER_IS_NOT_SET,
                RouterPluginException::CODE_PLUGIN_CONTROLLER_IS_NOT_SET
            );
        }

        if (empty($this->_annotationPlugin)) {
            throw new RouterPluginException(
                RouterPluginException::MESSAGE_PLUGIN_ANNOTATION_IS_NOT_SET,
                RouterPluginException::CODE_PLUGIN_ANNOTATION_IS_NOT_SET
            );
        }

        $routeParams = $this->_annotationPlugin->getAnnotation(
            $className,
            $methodName,
            'url_params'
        );

        $routeParams = preg_replace('/\s+/su', '', $routeParams);
        $routeParams = !empty($routeParams) ? $routeParams : null;

        return $routeParams;
    }
}
