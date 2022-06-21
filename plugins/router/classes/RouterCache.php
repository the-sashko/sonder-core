<?php

namespace Sonder\Plugins\Router\Classes;

use Sonder\Plugins\Router\Interfaces\IRouterCache;
use Sonder\Plugins\Router\Interfaces\IRouterEntity;

#[IRouterCache]
final class RouterCache implements IRouterCache
{
    private const CACHE_DIR_PATH = __DIR__ . '/../../../../cache/router';

    /**
     * @return array|null
     */
    final public function getRoutes(): ?array
    {
        if (defined('APP_MODE') && APP_MODE == 'dev') {
            return null;
        }

        if (defined('APP_API_MODE') && APP_API_MODE == 'dev') {
            return null;
        }

        $cacheFilePath = $this->_getCacheFilePath();

        if (!file_exists($cacheFilePath) || !is_file($cacheFilePath)) {
            return null;
        }

        $routes = file_get_contents($cacheFilePath);
        $routes = (array)json_decode($routes);
        $routes = array_map('unserialize', $routes);

        return !empty($routes) ? $routes : null;
    }

    /**
     * @param string $url
     * @return IRouterEntity|null
     */
    final public function getRouteByUrl(string $url): ?IRouterEntity
    {
        if (defined('APP_MODE') && APP_MODE == 'dev') {
            return null;
        }

        if (defined('APP_API_MODE') && APP_API_MODE == 'dev') {
            return null;
        }

        $cacheFilePath = $this->_getUrlCacheFilePath($url);

        if (!file_exists($cacheFilePath) || !is_file($cacheFilePath)) {
            return null;
        }

        $cacheData = (array)json_decode(
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

    /**
     * @param array $routes
     * @return void
     */
    final public function saveRoutes(array $routes): void
    {
        $routes = array_map('serialize', $routes);
        $routes = json_encode($routes);

        $cacheFilePath = $this->_getCacheFilePath();

        if (file_exists($cacheFilePath) && is_file($cacheFilePath)) {
            unlink($cacheFilePath);
        }

        file_put_contents($cacheFilePath, $routes);
    }

    /**
     * @param string $url
     * @param IRouterEntity $route
     * @return void
     */
    final public function saveRouteUrl(string $url, IRouterEntity $route): void
    {
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

    /**
     * @return void
     */
    final public function clean(): void
    {
        $cacheFilePath = $this->_getCacheFilePath();

        if (file_exists($cacheFilePath) && is_file($cacheFilePath)) {
            unlink($cacheFilePath);
        }

        $urlCacheFilesPattern = sprintf(
            '%s/url/*.json',
            $this->_getCacheDirPath()
        );

        foreach (glob($urlCacheFilesPattern) as $urlCacheFilePath) {
            if (is_file($urlCacheFilePath)) {
                unlink($urlCacheFilePath);
            }
        }
    }

    /**
     * @return string
     */
    private function _getCacheFilePath(): string
    {
        $cacheDirPath = $this->_getCacheDirPath();

        if (!file_exists($cacheDirPath) || !is_dir($cacheDirPath)) {
            mkdir($cacheDirPath, 0755, true);
        }

        return sprintf('%s/routes.json', $cacheDirPath);
    }

    /**
     * @param string $url
     * @return string
     */
    private function _getUrlCacheFilePath(string $url): string
    {
        $cacheDirPath = $this->_getCacheDirPath();

        return sprintf(
            '%s/url/%s.json',
            $cacheDirPath,
            sprintf(
                '%s%s',
                hash('sha256', $url),
                hash('md5', $url)
            )
        );
    }

    /**
     * @param string $cacheFilePath
     * @return void
     */
    private function _createCacheUrlCacheFile(string $cacheFilePath): void
    {
        $cacheDir = sprintf('%s/url', $this->_getCacheDirPath());

        if (!file_exists($cacheDir) || !is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        $cacheData = [
            'timestamp' => time(),
            'data' => []
        ];

        $cacheData = json_encode($cacheData);

        if (!file_exists($cacheFilePath) || !is_file($cacheFilePath)) {
            file_put_contents($cacheFilePath, $cacheData);
            chmod($cacheFilePath, 0755);
        }
    }

    /**
     * @return string
     */
    private function _getCacheDirPath(): string
    {
        if (!defined('APP_PROTECTED_DIR_PATH')) {
            return RouterCache::CACHE_DIR_PATH;
        }

        return sprintf('%s/cache/router', APP_PROTECTED_DIR_PATH);
    }
}
