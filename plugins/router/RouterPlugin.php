<?php

namespace Sonder\Plugins;

use Generator;
use ReflectionClass;
use Sonder\Plugins\Annotation\AnnotationException;
use Sonder\Plugins\Annotation\Interfaces\IAnnotationPlugin;
use Sonder\Plugins\Router\Classes\RouterCache;
use Sonder\Plugins\Router\Classes\RouterEntity;
use Sonder\Plugins\Router\RouterAnnotationNamesEnum;
use Sonder\Plugins\Router\RouterException;
use Sonder\Plugins\Router\Interfaces\IRouterCache;
use Sonder\Plugins\Router\Interfaces\IRouterEntity;
use Sonder\Plugins\Router\Interfaces\IRouterPlugin;

#[IRouterPlugin]
final class RouterPlugin implements IRouterPlugin
{
    private const DEFAULT_AREA = 'default';

    private const DEFAULT_CONTROLLERS_DIR_PATHS = [
        __DIR__ . '/../../../controllers'
    ];

    #[IRouterCache]
    private IRouterCache $_cache;

    #[IAnnotationPlugin]
    private IAnnotationPlugin $_annotationPlugin;

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
     * @return IRouterEntity|null
     * @throws AnnotationException
     * @throws RouterException
     */
    final public function getRoute(string $url = null): ?IRouterEntity
    {
        if (empty($url)) {
            throw new RouterException(
                RouterException::MESSAGE_URL_IS_NOT_SET,
                RouterException::CODE_URL_IS_NOT_SET
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

    /**
     * @return void
     */
    final public function cleanCache(): void
    {
        $this->_cache->clean();
    }

    /**
     * @return void
     */
    private function _setArea(): void
    {
        $this->_area = RouterPlugin::DEFAULT_AREA;

        if (defined('APP_AREA')) {
            $this->_area = APP_AREA;
        }
    }

    /**
     * @param array|null $controllersDirPaths
     * @return void
     */
    private function _setControllersDirPaths(
        ?array $controllersDirPaths = null
    ): void {
        if (empty($controllersDirPaths)) {
            $controllersDirPaths = $this->_getDefaultControllersDirPaths();
        }

        $this->_controllersDirPaths = $controllersDirPaths;
    }

    /**
     * @return array|string[]
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
     * @return IRouterEntity|null
     * @throws AnnotationException
     * @throws RouterException
     */
    private function _getRouteByUrl(string $url): ?IRouterEntity
    {
        $routeByUrl = null;

        if (empty($url)) {
            throw new RouterException(
                RouterException::MESSAGE_URL_IS_NOT_SET,
                RouterException::CODE_URL_IS_NOT_SET
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
     * @throws AnnotationException
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
     * @return array
     */
    private function _getControllersDisplayMethodsByDirPath(
        string $controllersDirPath
    ): array {
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
                empty($displayMethodsByClassName) ? [] : $displayMethodsByClassName
            );
        }

        return $displayMethods;
    }

    /**
     * @param string $className
     * @param string $filePath
     * @return Generator|null
     */
    private function _getDisplayMethodsByClassName(
        string $className,
        string $filePath
    ): ?Generator {
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
     * @return RouterEntity|null
     * @throws AnnotationException
     */
    private function _getRoutesFromClassMethod(
        array $classesMethod
    ): ?RouterEntity {
        $controllerClass = $classesMethod['class_name'];
        $controllerFilePath = $classesMethod['file_path'];
        $controllerMethod = $classesMethod['method'];

        if (empty($controllerClass) || empty($controllerFilePath) || empty($controllerMethod)) {
            return null;
        }

        $area = $this->_getRouteArea($controllerClass, $controllerMethod);
        $path = $this->_getRoutePath($controllerClass, $controllerMethod);
        $params = $this->_getRouteParams($controllerClass, $controllerMethod);
        $noCache = $this->_getRouteNoCache($controllerClass, $controllerMethod);

        if (empty($path)) {
            return null;
        }

        return new RouterEntity(
            $area,
            $path,
            $params,
            $noCache,
            $controllerClass,
            $controllerMethod,
            $controllerFilePath,
        );
    }

    /**
     * @param string $className
     * @param string $methodName
     * @return string
     * @throws AnnotationException
     */
    private function _getRouteArea(
        string $className,
        string $methodName
    ): string {
        $routeArea = $this->_annotationPlugin->getAnnotation(
            $className,
            $methodName,
            RouterAnnotationNamesEnum::AREA->value
        );

        $routeArea = mb_convert_case((string)$routeArea, MB_CASE_LOWER);

        $routeArea = preg_replace(
            '/([^a-z]+)/u',
            '',
            $routeArea
        );

        return !empty($routeArea) ? $routeArea : RouterEntity::DEFAULT_AREA;
    }

    /**
     * @param string $className
     * @param string $methodName
     * @return string|null
     * @throws AnnotationException
     */
    private function _getRoutePath(
        string $className,
        string $methodName
    ): ?string {
        $routePath = $this->_annotationPlugin->getAnnotation(
            $className,
            $methodName,
            RouterAnnotationNamesEnum::ROUTE->value
        );

        $routePath = preg_replace(
            '/\s+/u',
            '',
            (string)$routePath
        );

        return !empty($routePath) ? $routePath : null;
    }

    /**
     * @param string $className
     * @param string $methodName
     * @return string|null
     * @throws AnnotationException
     */
    private function _getRouteParams(
        string $className,
        string $methodName
    ): ?string {
        $routeParams = $this->_annotationPlugin->getAnnotation(
            $className,
            $methodName,
            RouterAnnotationNamesEnum::URL_PARAMS->value
        );

        $routeParams = preg_replace(
            '/\s+/u',
            '',
            (string)$routeParams
        );

        return !empty($routeParams) ? $routeParams : null;
    }

    /**
     * @param string $className
     * @param string $methodName
     * @return bool
     * @throws AnnotationException
     */
    private function _getRouteNoCache(
        string $className,
        string $methodName
    ): bool {
        $noCache = $this->_annotationPlugin->getAnnotation(
            $className,
            $methodName,
            RouterAnnotationNamesEnum::NO_CACHE->value
        );

        $noCache = mb_convert_case((string)$noCache, MB_CASE_LOWER);

        $noCache = preg_replace(
            '/([^a-z]+)/u',
            '',
            $noCache
        );

        $noCache = !empty($noCache) ? $noCache : 'false';

        return $noCache == 'true';
    }
}
