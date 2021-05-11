<?php
namespace Core\Plugins\Router\Classes;

use Core\Plugins\Router\Interfaces\IRouterCache;
use Core\Plugins\Router\Interfaces\IRouterEntity;

use Core\Plugins\Router\Exceptions\RouterCacheException;

class RouterCache implements IRouterCache
{
    const CACHE_DIR_PATH = __DIR__.'/../../../../res/cache/router';

    public function getRoutes(): ?array
    {
        $cacheFilePath = $this-> _getRoutesCacheFilePath();

        if (!file_exists($cacheFilePath) || !is_file($cacheFilePath)) {
            return null;
        }

        $routes = file_get_contents($cacheFilePath);
        $routes = (array) json_decode($routes);
        $routes = array_map('unserialize', $routes);
        $routes = !empty($routes) ? $routes : null;

        return $routes;
    }

    public function getRouteByUrl(string $url): ?IRouterEntity
    {
        if (empty($url)) {
            throw new RouterCacheException(
                RouterCacheException::MESSAGE_CACHE_URL_IS_NOT_SET,
                RouterCacheException::CODE_CACHE_URL_IS_NOT_SET
            );
        }

        $cacheFilePath = $this->_getUrlCacheFilePath($url);

        if (!file_exists($cacheFilePath) || !is_file($cacheFilePath)) {
            return null;
        }

        $cacheData = (array) json_decode(
            file_get_contents($cacheFilePath),
            true
        );

        if (
            array_key_exists('data', $cacheData) &&
            !empty($cacheData['data']) &&
            is_array($cacheData['data']) &&
            array_key_exists(APP_AREA, $cacheData['data']) &&
            !empty($cacheData['data'][APP_AREA])
        ) {
            return unserialize($cacheData['data'][APP_AREA]);
        }

        return null;
    }

    public function saveRoutes(?array $routes = null): void
    {
        $routes = array_map('serialize', (array) $routes);
        $routes = json_encode($routes);

        $cacheFilePath = $this-> _getRoutesCacheFilePath();

        if (file_exists($cacheFilePath) && is_file($cacheFilePath)) {
            unlink($cacheFilePath);
        }

        file_put_contents($cacheFilePath, $routes);
    }

    public function saveRouteUrl(
        ?string        $url   = null,
        ?IRouterEntity $route = null
    ): void
    {
        if (empty($url)) {
            throw new RouterCacheException(
                RouterCacheException::MESSAGE_CACHE_URL_IS_NOT_SET,
                RouterCacheException::CODE_CACHE_URL_IS_NOT_SET
            );
        }

        if (empty($route)) {
            throw new RouterCacheException(
                RouterCacheException::MESSAGE_CACHE_ROUTE_IS_NOT_SET,
                RouterCacheException::CODE_CACHE_ROUTE_IS_NOT_SET
            );
        }

        $serializedRoute = serialize($route);

        $cacheFilePath = $this->_getUrlCacheFilePath($url);

        if (!file_exists($cacheFilePath) || !is_file($cacheFilePath)) {
            $this->_createCacheUrlCacheFile($cacheFilePath);
        }

        $cacheData = json_decode(file_get_contents($cacheFilePath), true);

        $cacheData['timestamp'] = time();

        if (
            !array_key_exists('data', $cacheData) ||
            empty($cacheData['data']) ||
            !is_array($cacheData['data'])
        ) {
            $cacheData['data'] = [];
        }

        $cacheData['data'][APP_AREA] = $serializedRoute;

        file_put_contents($cacheFilePath, json_encode($cacheData));
    }

    public function clean(): void
    {
        $cacheFilePath = $this-> _getRoutesCacheFilePath();

        if (file_exists($cacheFilePath) && is_file($cacheFilePath)) {
            unlink($cacheFilePath);
        }

        $urlCacheFilesPattern = sprintf(
            '%s/url/*.json',
            static::CACHE_DIR_PATH
        );

        foreach (glob($urlCacheFilesPattern) as $urlCacheFilePath) {
            if (is_file($urlCacheFilePath)) {
                unlink($urlCacheFilePath);
            }
        }
    }

    private function _getRoutesCacheFilePath(): string
    {
        if (
            !file_exists(static::CACHE_DIR_PATH) ||
            !is_dir(static::CACHE_DIR_PATH)
        ) {
            mkdir(static::CACHE_DIR_PATH, 0755, true);
        }

        return sprintf('%s/routes.json', static::CACHE_DIR_PATH);
    }

    private function _getUrlCacheFilePath(string $url): string
    {
        return sprintf(
            '%s/url/%s.json',
            static::CACHE_DIR_PATH,
            sprintf('%s%s', hash('sha256', $url), hash('md5', $url))
        );
    }

    private function _createCacheUrlCacheFile(string $cacheFilePath): void
    {
        $cacheDir = sprintf('%s/url', static::CACHE_DIR_PATH);

        if (!file_exists($cacheDir) || !is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        $cacheData = [
            'timestamp' => time(),
            'data'      => []
        ];

        $cacheData = json_encode($cacheData);

        if (!file_exists($cacheFilePath) || !is_file($cacheFilePath)) {
            file_put_contents($cacheFilePath, $cacheData);
            chmod($cacheFilePath, 0755);
        }
    }
}
