<?php

namespace Sonder\Plugins;

use Generator;
use ReflectionClass;
use Sonder\Plugins\Annotation\Exceptions\AnnotationPluginException;
use Sonder\Plugins\Router\Classes\RouterCache;
use Sonder\Plugins\Router\Classes\RouterEntity;
use Sonder\Plugins\Router\Exceptions\RouterException;
use Sonder\Plugins\Router\Exceptions\RouterPluginException;
use Sonder\Plugins\Router\Interfaces\IRouterEntity;
use Sonder\Plugins\Router\Interfaces\IRouterPlugin;

final class RouterPlugin implements IRouterPlugin
{
    const DEFAULT_AREA = 'default';

    const DEFAULT_CONTROLLERS_DIR_PATHS = [
        __DIR__ . '/../../../controllers'
    ];

    private RouterCache $_cache;

    private AnnotationPlugin $_annotationPlugin;

    private string $_area;

    private array $_controllersDirPaths;

    private ?string $_language = null;

    /**
     * @param array|null $controllerDirPaths
     */
    final public function __construct(?array $controllerDirPaths = null)
    {
        $this->_annotationPlugin = new AnnotationPlugin();
        $this->_cache = new RouterCache();

        $this->_setArea();
        $this->_setControllersDirPaths($controllerDirPaths);
    }

    /**
     * @param string|null $url
     *
     * @return IRouterEntity|null
     *
     * @throws AnnotationPluginException
     * @throws RouterPluginException
     * @throws Router\Exceptions\RouterCacheException
     */
    final public function getRoute(?string $url = null): ?IRouterEntity
    {
        if (empty($url)) {
            throw new RouterPluginException(
                RouterPluginException::MESSAGE_PLUGIN_URL_IS_NOT_SET,
                RouterException::CODE_PLUGIN_URL_IS_NOT_SET
            );
        }

        $url = explode('#', $url);
        $url = array_shift($url);
        $url = explode('&', $url);
        $url = array_shift($url);
        $url = explode('?', $url);
        $url = array_shift($url);

        $url = $this->_extractLanguageFromUrl($url);

        $route = $this->_cache->getRouteByUrl($url);

        if (empty($route)) {
            $route = $this->_getRouteByUrl($url);
        }

        if (!empty($route)) {
            $route->setLanguage($this->_language);

            $this->_cache->saveRouteUrl($url, $route);
        }

        return $route;
    }

    final public function cleanCache(): void
    {
        $this->_cache->clean();
    }

    private function _setArea(): void
    {
        $this->_area = RouterPlugin::DEFAULT_AREA;

        if (defined('APP_AREA')) {
            $this->_area = APP_AREA;
        }
    }

    /**
     * @param array|null $controllersDirPaths
     */
    private function _setControllersDirPaths(
        ?array $controllersDirPaths = null
    ): void
    {
        if (empty($controllersDirPaths)) {
            $controllersDirPaths = $this->_getDefaultControllersDirPaths();
        }

        $this->_controllersDirPaths = $controllersDirPaths;
    }

    /**
     * @return array
     */
    private function _getDefaultControllersDirPaths(): array
    {
        if (!defined('APP_PROTECTED_DIR_PATH')) {
            return RouterPlugin::DEFAULT_CONTROLLERS_DIR_PATHS;
        }

        return [
            sprintf('%s/controllers', APP_PROTECTED_DIR_PATH)
        ];
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
     * @return IRouterEntity|null
     *
     * @throws AnnotationPluginException
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

        $routes = $this->_cache->getRoutes();

        if (empty($routes)) {
            $routes = $this->_getRoutesFromAnnotations();
            $routes = empty($routes) ? [] : iterator_to_array($routes);

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
     * @return Generator|null
     *
     * @throws AnnotationPluginException
     */
    private function _getRoutesFromAnnotations(): ?Generator
    {
        foreach ($this->_getControllersDisplayMethods() as $controllerMethods) {
            $routesByAnnotations = $this->_getRoutesFromClassMethod(
                $controllerMethods
            );

            if (!empty($routesByAnnotations)) {
                yield $routesByAnnotations;
            }
        }

        return null;
    }

    /**
     * @return array
     */
    private function _getControllersDisplayMethods(): array
    {
        $displayMethods = [];

        foreach ($this->_controllersDirPaths as $controllersDirPath) {
            $displayMethods = array_merge(
                $displayMethods,
                $this->_getControllersDisplayMethodsByDirPath(
                    $controllersDirPath
                )
            );
        }

        return $displayMethods;
    }

    /**
     * @param string $controllersDirPath
     *
     * @return array
     */
    private function _getControllersDisplayMethodsByDirPath(
        string $controllersDirPath
    ): array
    {
        $displayMethods = [];

        $controllersFilePathPattern = sprintf(
            '%s/*Controller.php',
            $controllersDirPath
        );

        foreach (glob($controllersFilePathPattern) as $controllerFile) {
            $controllerClass = preg_replace(
                '/^(.*?)\/([A-z]+)\.php$/su',
                '$2',
                $controllerFile
            );

            $controllerClass = sprintf(
                'Sonder\Controllers\%s',
                $controllerClass
            );

            require_once $controllerFile;

            $displayMethodsByClassName = $this->_getDisplayMethodsByClassName(
                $controllerClass,
                $controllerFile
            );

            if (!empty($displayMethodsByClassName)) {
                $displayMethodsByClassName = iterator_to_array(
                    $displayMethodsByClassName
                );
            }

            $displayMethods = array_merge(
                $displayMethods,
                $displayMethodsByClassName ?? []
            );
        }

        return $displayMethods;
    }

    /**
     * @param string $className
     * @param string $filePath
     *
     * @return Generator|null
     */
    private function _getDisplayMethodsByClassName(
        string $className,
        string $filePath
    ): ?Generator
    {
        if (!class_exists($className)) {
            return null;
        }

        $reflection = new ReflectionClass($className);

        foreach ($reflection->getMethods() as $method) {
            if (!$method->isPublic()) {
                continue;
            }

            $method = $method->name;

            if (!preg_match('/^display(.*?)$/su', $method)) {
                continue;
            }


            yield [
                'file_path' => $filePath,
                'class_name' => $className,
                'method' => $method
            ];
        }

        return null;
    }

    /**
     * @param array $classesMethod
     *
     * @return RouterEntity|null
     *
     * @throws AnnotationPluginException
     */
    private function _getRoutesFromClassMethod(
        array $classesMethod
    ): ?RouterEntity
    {
        $controllerClass = $classesMethod['class_name'];
        $controllerFilePath = $classesMethod['file_path'];
        $method = $classesMethod['method'];

        $area = $this->_getRouteArea($controllerClass, $method);
        $path = $this->_getRoutePath($controllerClass, $method);
        $params = $this->_getRouteParams($controllerClass, $method);
        $noCache = $this->_getRouteNoCache($controllerClass, $method);

        if (empty($path)) {
            return null;
        }

        return new RouterEntity(
            $area,
            $path,
            $params,
            $controllerClass,
            $controllerFilePath,
            $method,
            $noCache
        );
    }

    /**
     * @param string $className
     * @param string $methodName
     *
     * @return string
     *
     * @throws AnnotationPluginException
     */
    private function _getRouteArea(
        string $className,
        string $methodName
    ): string
    {
        $routeArea = $this->_annotationPlugin->getAnnotation(
            $className,
            $methodName,
            'area'
        );

        $routeArea = mb_convert_case((string)$routeArea, MB_CASE_LOWER);

        $routeArea = preg_replace(
            '/([^a-z]+)/su',
            '',
            $routeArea
        );

        return !empty($routeArea) ? $routeArea : RouterEntity::DEFAULT_AREA;
    }

    /**
     * @param string $className
     * @param string $methodName
     *
     * @return string|null
     *
     * @throws AnnotationPluginException
     */
    private function _getRoutePath(
        string $className,
        string $methodName
    ): ?string
    {
        $routePath = $this->_annotationPlugin->getAnnotation(
            $className,
            $methodName,
            'route'
        );

        $routePath = preg_replace(
            '/\s+/su',
            '',
            (string)$routePath
        );

        return !empty($routePath) ? $routePath : null;
    }

    /**
     * @param string|null $className
     * @param string|null $methodName
     *
     * @return string|null
     *
     * @throws AnnotationPluginException
     */
    private function _getRouteParams(
        string $className = null,
        string $methodName = null
    ): ?string
    {
        $routeParams = $this->_annotationPlugin->getAnnotation(
            $className,
            $methodName,
            'url_params'
        );

        $routeParams = preg_replace(
            '/\s+/su',
            '',
            (string)$routeParams
        );

        return !empty($routeParams) ? $routeParams : null;
    }

    /**
     * @param string|null $className
     * @param string|null $methodName
     *
     * @return bool
     *
     * @throws AnnotationPluginException
     */
    private function _getRouteNoCache(
        ?string $className = null,
        ?string $methodName = null
    ): bool
    {
        $noCache = $this->_annotationPlugin->getAnnotation(
            $className,
            $methodName,
            'no_cache'
        );

        $noCache = mb_convert_case((string)$noCache, MB_CASE_LOWER);

        $noCache = preg_replace(
            '/([^a-z]+)/su',
            '',
            $noCache
        );

        $noCache = !empty($noCache) ? $noCache : 'false';

        return $noCache == 'true';
    }
}
