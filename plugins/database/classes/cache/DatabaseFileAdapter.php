<?php

namespace Sonder\Plugins\Database\Classes;

use Sonder\Plugins\Database\Interfaces\IDataBaseCacheAdapter;

/**
 * Data Base Cache Provider For Files
 */
final class DatabaseFileCacheAdapter implements IDataBaseCacheAdapter
{
    private const CACHE_DIR_PATH = __DIR__ . '/../../../../../cache/db';

    /**
     * @param string $sql
     * @param array $data
     * @param string $scope
     * @param int $ttl
     *
     * @return bool
     */
    final public function set(
        string $sql,
        array  $data,
        string $scope,
        int    $ttl
    ): bool
    {
        $cacheFilePath = $this->_getCacheFilePath($sql, $scope);

        $content = base64_encode(json_encode($data));

        $cacheData = [
            'time' => time() + $ttl,
            'content' => $content
        ];

        $cacheData = json_encode($cacheData);

        if (file_exists($cacheFilePath) && is_file($cacheFilePath)) {
            unlink($cacheFilePath);
        }

        $cacheDirectoryPath = DatabaseFileCacheAdapter::_getCacheDirectoryPath();
        $cacheDirectoryPath = sprintf('%s/%s', $cacheDirectoryPath, $scope);

        if (!file_exists($cacheDirectoryPath) || !is_dir($cacheDirectoryPath)) {
            mkdir($cacheDirectoryPath, 0775, true);
        }

        file_put_contents($cacheFilePath, $cacheData);

        return true;
    }

    /**
     * @param string $sql
     * @param string $scope
     *
     * @return array|null
     */
    final public function get(string $sql, string $scope): ?array
    {
        $cacheFilePath = $this->_getCacheFilePath($sql, $scope);

        return $this->_getDataFromCache($cacheFilePath);
    }

    /**
     * @param string $scope
     *
     * @return bool
     */
    final public function clean(string $scope): bool
    {
        $cacheDirectoryPath = $this->_getCacheDirectoryPath();
        $cacheDirectoryPath = sprintf('%s/%s', $cacheDirectoryPath, $scope);

        if (!file_exists($cacheDirectoryPath) || !is_dir($cacheDirectoryPath)) {
            return false;
        }

        $cacheFiles = scandir($cacheDirectoryPath);

        foreach ($cacheFiles as $cacheFile) {
            if ('.' == $cacheFile || '..' == $cacheFile) {
                continue;
            }

            $cacheFilePath = sprintf('%s/%s', $cacheDirectoryPath, $cacheFile);

            if (is_file($cacheFilePath)) {
                unlink($cacheFilePath);
            }
        }

        return true;
    }

    /**
     * @param string $sql
     * @param string $scope
     *
     * @return string
     */
    private function _getCacheFilePath(string $sql, string $scope): string
    {
        $hash = sprintf(
            '%s%s%s',
            hash('md5', $sql),
            hash('sha512', $sql),
            hash('md5', sprintf('%s%s', $scope, $sql))
        );

        $cacheDirectoryPath = $this->_getCacheDirectoryPath();

        return sprintf('%s/%s/%s', $cacheDirectoryPath, $scope, $hash);
    }

    /**
     * @param string $cacheFilePath
     *
     * @return array|null
     */
    private function _getDataFromCache(string $cacheFilePath): ?array
    {
        if (!file_exists($cacheFilePath) || !is_file($cacheFilePath)) {
            return null;
        }

        $cacheData = file_get_contents($cacheFilePath);
        $cacheData = json_decode($cacheData, true);

        if (!$this->_validateCache($cacheData)) {
            unlink($cacheFilePath);

            return null;
        }

        if (
            !array_key_exists('content', $cacheData) ||
            empty($cacheData['content'])
        ) {
            return null;
        }

        $cacheData = base64_decode($cacheData['content']);

        return (array)json_decode($cacheData, true);
    }

    /**
     * @param $cacheData
     *
     * @return bool
     */
    private function _validateCache($cacheData): bool
    {
        if (!array_key_exists('time', $cacheData)) {
            return false;
        }

        if (!array_key_exists('content', $cacheData)) {
            return false;
        }

        return intval($cacheData['time']) > time();
    }

    /**
     * @return string
     */
    private function _getCacheDirectoryPath(): string
    {
        if (defined('APP_PROTECTED_DIR_PATH')) {
            return sprintf('%s/cache/db', APP_PROTECTED_DIR_PATH);
        }

        return DatabaseFileCacheAdapter::CACHE_DIR_PATH;
    }
}
