<?php

namespace Sonder\Plugins;

use Generator;
use ReflectionClass;
use ReflectionException;
use Sonder\Plugins\Annotation\Exceptions\AnnotationPluginException;
use Sonder\Plugins\Router\Classes\RouterCache;
use Sonder\Plugins\Router\Classes\RouterEntity;
use Sonder\Plugins\Router\Exceptions\RouterCacheException;
use Sonder\Plugins\Router\Exceptions\RouterException;
use Sonder\Plugins\Router\Exceptions\RouterPluginException;
use Sonder\Plugins\Router\Interfaces\IRouterEntity;
use Sonder\Plugins\Router\Interfaces\IRouterPlugin;

final class RouterPlugin implements IRouterPlugin
{
    const DEFAULT_CONTROLLERS_DIR_PATH = __DIR__ . '/../../../controllers';

    const DEFAULT_AREA = 'default';

    /**
     * @var AnnotationPlugin
     */
    private AnnotationPlugin $_annotationPlugin;

    /**
     * @var string
     */
    private string $_controllersDirPath;

    /**
     * @var RouterCache
     */
    private RouterCache $_cache;

    /**
     * @var string|null
     */
    private ?string $_language = null;

    /**
     * @var int|null
     */
    private ?int $_page = null;

    /**
     * @var string|null
     */
    private ?string $_area = null;

    /**
     * @param string|null $controllerDirPath
     */
    final public function __construct(?string $controllerDirPath = null)
    {
        $this->_annotationPlugin = new AnnotationPlugin();

        $this->_cache = new RouterCache();

        $this->_setArea();
        $this->_setControllersDirPath($controllerDirPath);
    }

    /**
     * @param string|null $url
     *
     * @return IRouterEntity|null
     *
     * @throws AnnotationPluginException
     * @throws ReflectionException
     * @throws RouterCacheException
     * @throws RouterPluginException
     */
    public function getRoute(?string $url = null): ?IRouterEntity
    {
        if (empty($url)) {
            throw new RouterPluginException(
                RouterPluginException::MESSAGE_PLUGIN_URL_IS_NOT_SET,
                RouterException::CODE_PLUGIN_URL_IS_NOT_SET
            );
        }

        if (empty($this->_cache)) {
            throw new RouterPluginException(
                RouterPluginException::MESSAGE_PLUGIN_CACHE_IS_NOT_SET,
                RouterException::CODE_PLUGIN_CACHE_IS_NOT_SET
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

    /**
     * @throws RouterPluginException
     */
    public function cleanCache(): void
    {
        if (empty($this->_cache)) {
            throw new RouterPluginException(
                RouterPluginException::MESSAGE_PLUGIN_CACHE_IS_NOT_SET,
                RouterException::CODE_PLUGIN_CACHE_IS_NOT_SET
            );
        }

        $this->_cache->clean();
    }

    /**
     * @param string $url
     *
     * @return string
     */
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

            $url = preg_replace(
                '/^\/([a-z]{2})\/(.*?)$/su',
                '/$2',
                $url
            );
        }

        return !empty($url) ? $url : '/';
    }

    /**
     * @param string $url
     *
     * @return string
     */
    private function _extractPageFromUrl(string $url): string
    {
        if (preg_match('/^(.*?)\/page-([0-9]+)\/$/su', $url)) {
            $this->_page = (int)preg_replace(
                '/^(.*?)\/page-([0-9]+)\/$/su',
                '$2',
                $url
            );

            $url = preg_replace(
                '/^(.*?)\/page-([0-9]+)\/$/su',
                '$1/',
                $url
            );
        }

        return !empty($url) ? $url : '/';
    }

    /**
     * @param string $url
     *
     * @return IRouterEntity|null
     *
     * @throws AnnotationPluginException
     * @throws ReflectionException
     * @throws RouterPluginException
     */
    private function _getRouteByUrl(string $url): ?IRouterEntity
    {
        $routeByUrl = null;

        if (empty($url)) {
            throw new RouterPluginException(
                RouterPluginException::MESSAGE_PLUGIN_URL_IS_NOT_SET,
                RouterException::CODE_PLUGIN_URL_IS_NOT_SET
            );
        }

        if (empty($this->_cache)) {
            throw new RouterPluginException(
                RouterPluginException::MESSAGE_PLUGIN_CACHE_IS_NOT_SET,
                RouterException::CODE_PLUGIN_CACHE_IS_NOT_SET
            );
        }

        $routes = $this->_cache->getRoutes();

        if (empty($routes)) {
            $routes = $this->_getRoutesFromAnnotations();
            $routes = iterator_to_array($routes);

            $this->_cache->saveRoutes($routes);
        }

        foreach ($routes as $route) {
            if (
                preg_match($route->getRoutePattern(), $url) &&
                $route->getArea() == $this->_area
            ) {
                $routeByUrl = $route;
            }
        }

        return $routeByUrl;
    }

    /**
     * @return Generator
     *
     * @throws AnnotationPluginException
     * @throws ReflectionException
     * @throws RouterPluginException
     */
    private function _getRoutesFromAnnotations(): Generator
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

    /**
     * @param array|null $methods
     *
     * @return Generator
     *
     * @throws AnnotationPluginException
     * @throws RouterPluginException
     */
    private function _getRoutesFromClassMethods(
        ?array $methods = null
    ): Generator
    {
        $methods = (array)$methods;

        $controllerClass = null;

        if (array_key_exists('controller', $methods)) {
            $controllerClass = (string)$methods['controller'];
        }

        if (array_key_exists('methods', $methods)) {
            $methods = $methods['methods'];
        }

        foreach ($methods as $method) {
            $routeArea = $this->_getRouteArea($controllerClass, $method);
            $routePath = $this->_getRoutePath($controllerClass, $method);
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
    }

    /**
     * @return Generator
     *
     * @throws ReflectionException
     * @throws RouterPluginException
     */
    private function _getControllersDisplayMethods(): Generator
    {
        $controllersFilePathPattern = sprintf(
            '%s/*Controller.php',
            $this->_controllersDirPath
        );

        foreach (glob($controllersFilePathPattern) as $controllerFile) {
            $controllerClass = preg_replace(
                '/^(.*?)\/([A-z]+)\.php$/su',
                '$2',
                $controllerFile
            );

            require_once $controllerFile;

            $methods = $this->_getDisplayMethodsByClassName($controllerClass);

            yield [
                'controller' => $controllerClass,
                'methods' => $methods
            ];
        }
    }

    /**
     * @param string|null $className
     *
     * @return Generator
     *
     * @throws RouterPluginException
     * @throws ReflectionException
     */
    private function _getDisplayMethodsByClassName(
        ?string $className = null
    ): Generator
    {
        if (empty($className)) {
            throw new RouterPluginException(
                RouterPluginException::MESSAGE_PLUGIN_CONTROLLER_IS_NOT_SET,
                RouterException::CODE_PLUGIN_CONTROLLER_IS_NOT_SET
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

    /**
     * @param string|null $className
     * @param string|null $methodName
     *
     * @return string
     *
     * @throws AnnotationPluginException
     * @throws RouterPluginException
     */
    private function _getRouteArea(
        ?string $className = null,
        ?string $methodName = null
    ): string
    {
        if (empty($className)) {
            throw new RouterPluginException(
                RouterPluginException::MESSAGE_PLUGIN_CONTROLLER_IS_NOT_SET,
                RouterException::CODE_PLUGIN_CONTROLLER_IS_NOT_SET
            );
        }

        if (empty($this->_annotationPlugin)) {
            throw new RouterPluginException(
                RouterPluginException::MESSAGE_PLUGIN_ANNOTATION_IS_NOT_SET,
                RouterException::CODE_PLUGIN_ANNOTATION_IS_NOT_SET
            );
        }

        $routeArea = $this->_annotationPlugin->getAnnotation(
            $className,
            $methodName,
            'area'
        );

        $routeArea = mb_convert_case((string)$routeArea, MB_CASE_LOWER);
        $routeArea = preg_replace('/([^a-z]+)/su', '', $routeArea);

        return !empty($routeArea) ? $routeArea : RouterEntity::DEFAULT_AREA;
    }

    /**
     * @param string|null $className
     * @param string|null $methodName
     *
     * @return string|null
     *
     * @throws AnnotationPluginException
     * @throws RouterPluginException
     */
    private function _getRoutePath(
        ?string $className = null,
        ?string $methodName = null
    ): ?string
    {
        if (empty($className)) {
            throw new RouterPluginException(
                RouterPluginException::MESSAGE_PLUGIN_CONTROLLER_IS_NOT_SET,
                RouterException::CODE_PLUGIN_CONTROLLER_IS_NOT_SET
            );
        }

        if (empty($this->_annotationPlugin)) {
            throw new RouterPluginException(
                RouterPluginException::MESSAGE_PLUGIN_ANNOTATION_IS_NOT_SET,
                RouterException::CODE_PLUGIN_ANNOTATION_IS_NOT_SET
            );
        }

        $routePath = $this->_annotationPlugin->getAnnotation(
            $className,
            $methodName,
            'route'
        );

        $routePath = preg_replace('/\s+/su', '', (string)$routePath);

        return !empty($routePath) ? $routePath : null;
    }

    /**
     * @param string|null $className
     * @param string|null $methodName
     *
     * @return string|null
     *
     * @throws AnnotationPluginException
     * @throws RouterPluginException
     */
    private function _getRouteParams(
        ?string $className = null,
        ?string $methodName = null
    ): ?string
    {
        if (empty($className)) {
            throw new RouterPluginException(
                RouterPluginException::MESSAGE_PLUGIN_CONTROLLER_IS_NOT_SET,
                RouterException::CODE_PLUGIN_CONTROLLER_IS_NOT_SET
            );
        }

        if (empty($this->_annotationPlugin)) {
            throw new RouterPluginException(
                RouterPluginException::MESSAGE_PLUGIN_ANNOTATION_IS_NOT_SET,
                RouterException::CODE_PLUGIN_ANNOTATION_IS_NOT_SET
            );
        }

        $routeParams = $this->_annotationPlugin->getAnnotation(
            $className,
            $methodName,
            'url_params'
        );

        $routeParams = preg_replace('/\s+/su', '', (string)$routeParams);

        return !empty($routeParams) ? $routeParams : null;
    }

    private function _setArea(): void
    {
        $this->_area = RouterPlugin::DEFAULT_AREA;

        if (defined('APP_AREA')) {
            $this->_area = APP_AREA;
        }
    }

    /**
     * @param string|null $controllersDirPath
     */
    private function _setControllersDirPath(
        ?string $controllersDirPath = null
    ): void
    {
        if (empty($controllersDirPath)) {
            $controllersDirPath = $this->_getDefaultControllersDirPath();
        }

        $this->_controllersDirPath = $controllersDirPath;
    }

    /**
     * @return string
     */
    private function _getDefaultControllersDirPath(): string
    {
        if (!defined('APP_PROTECTED_DIR_PATH')) {
            return RouterPlugin::DEFAULT_CONTROLLERS_DIR_PATH;
        }

        return sprintf('%s/controllers', APP_PROTECTED_DIR_PATH);
    }
}
