<?php
namespace Core\Plugins\Database\Classes;

use Core\Plugins\Database\Interfaces\IDataBaseCacheAdapter;

/**
 * Data Base Cahe Provider For Files
 */
class DatabaseFileCacheAdapter implements IDataBaseCacheAdapter
{
    /**
     * @var string Cache Files Directory
     */
    const DB_CACHE_DIR = __DIR__.'/../../../../../res/cache/db';

    /**
     * Save Cached Data To File
     *
     * @param string $sql   SQL Query
     * @param array  $data  Data Base Data
     * @param string $scope Scope Of Data Base Request
     * @param int    $ttl   Time To Live Of Cache
     *
     * @return bool Is Successfully Saved Cached Data
     */
    public function set(
        string $sql,
        array  $data,
        string $scope,
        int    $ttl
    ): bool
    {
        $cacheFilePath = $this->_getCacheFilePath($sql, $scope);

        $content = base64_encode(json_encode($data));

        $cacheData = [
            'time'    => time()+$ttl,
            'content' => $content
        ];

        $cacheData = json_encode($cacheData);

        if (file_exists($cacheFilePath) && is_file($cacheFilePath)) {
            unlink($cacheFilePath);
        }

        if (
            !file_exists(static::DB_CACHE_DIR.'/'.$scope) ||
            !is_dir(static::DB_CACHE_DIR.'/'.$scope)
        ) {
            mkdir(static::DB_CACHE_DIR.'/'.$scope, 0775, true);
        }

        file_put_contents($cacheFilePath, $cacheData);

        return true;
    }

    /**
     * Get Cached Data From File
     *
     * @param string $sql   SQL Query
     * @param string $scope Scope Of Data Base Request
     *
     * @return array Cached Data
     */
    public function get(string $sql, string $scope): ?array
    {
        $cacheFilePath = $this->_getCacheFilePath($sql, $scope);

        return $this->_getDataFromCache($cacheFilePath);
    }

    /**
     * Remove All Cache Files Of Data Base Request Scope
     *
     * @param string $scope Scope Of Data Base Request
     *
     * @return bool Is Successfully Removed Cached Data
     */
    public function clean(string $scope): bool
    {
        if (
            !file_exists(static::DB_CACHE_DIR.'/'.$scope) ||
            !is_dir(static::DB_CACHE_DIR.'/'.$scope)
        ) {
            return false;
        }

        $cacheFiles = scandir(static::DB_CACHE_DIR.'/'.$scope);

        foreach ($cacheFiles as $cacheFile) {
            if (
                '.' == $cacheFile ||
                '..' == $cacheFile
            ) {
                continue;
            }

            $cacheFilePath = static::DB_CACHE_DIR.'/'.$scope.'/'.$cacheFile;

            if (is_file($cacheFilePath)) {
                unlink($cacheFilePath);
            }
        }

        return true;
    }

    /**
     * Get Cache File Path
     *
     * @param string $sql   SQL Query
     * @param string $scope Scope Of Data Base Request
     *
     * @return string Value
     */
    private function _getCacheFilePath(string $sql, string $scope): string
    {
        $hash = hash('md5', $sql).
                hash('sha512', $sql).
                hash('md5', $scope.$sql);

        return static::DB_CACHE_DIR.'/'.$scope.'/'.$hash;
    }

    /**
     * Get Data From Cache File
     *
     * @param string $cacheFilePath Cache File Path
     *
     * @return array Cached Data
     */
    private function _getDataFromCache(string $cacheFilePath): ?array
    {
        if (!file_exists($cacheFilePath) || !is_file($cacheFilePath) ) {
            return null;
        }

        $cacheData = file_get_contents($cacheFilePath);
        $cacheData = json_decode($cacheData, true);

        if (!$this->_validateCache($cacheData)) {
            unlink($cacheFilePath);

            return null;
        }

        $cacheData = base64_decode($cacheData['content']);
        $cacheData = (array) json_decode($cacheData, true);

        return $cacheData;
    }

    /**
     * Check Is Cached Data Valid
     *
     * @param array $cacheData Cached Data
     *
     * @return bool Is Cached Data Valid
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
}
