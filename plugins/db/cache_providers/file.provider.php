<?php
/**
 * Data Base Cahe Provider For Files
 */
class DBFileCacheProvider
{
    /**
     * @var string Cache Files Directory
     */
    const DB_CACHE_DIR = __DIR__.'/../../../../res/cache/db';

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
        string $sql   = '',
        array  $data  = [],
        string $scope = 'default',
        int    $ttl   = -1
    ) : bool
    {
        $cacheFilePath = $this->_getCacheFilePath($sql, $scope);

        $content = base64_encode(json_encode($data));

        $cacheData = [
            'time' => time()+$ttl,
            'content' => $content
        ];

        if (is_file($cacheFilePath)) {
            unlink($cacheFilePath);
        }

        if (!is_dir($this::DB_CACHE_DIR.'/'.$scope)) {
            mkdir($this::DB_CACHE_DIR.'/'.$scope);
            chmod($this::DB_CACHE_DIR.'/'.$scope, 0775);
        }

        file_put_contents($cacheFilePath, json_encode($cacheData));
        chmod($cacheFilePath, 0775);

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
    public function get(
        string $sql   = '',
        string $scope = 'default'
    ) : array
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
    public function flush(string $scope = 'default') : bool
    {
        if (!is_dir($this::DB_CACHE_DIR.'/'.$scope)) {
            return false;
        }

        foreach (scandir($this::DB_CACHE_DIR.'/'.$scope) as $fileItem) {
            if (
                $fileItem!='.' &&
                $fileItem!='..' &&
                is_file($this::DB_CACHE_DIR.$scope.'/'.$fileItem)
            ) {
                unlink($this::DB_CACHE_DIR.$scope.'/'.$fileItem);
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
    private function _getCacheFilePath(
        string $sql = '',
        string $scope = ''
    ) : string
    {
        $hash = hash('md5', $sql).
                hash('sha512', $sql).
                hash('md5', $scope.$sql);

        return $this::DB_CACHE_DIR.'/'.$scope.'/'.$hash;
    }

    /**
     * Get Data From Cache File
     *
     * @param string $cacheFilePath Cache File Path
     *
     * @return array Cached Data
     */
    private function _getDataFromCache(string $cacheFilePath = '') : array
    {
        if (!is_file($cacheFilePath)) {
            return [];
        }

        $cacheData = file_get_contents($cacheFilePath);
        $cacheData = json_decode($cacheData,true);

        if (!$this->_validateCache($cacheData)) {
            unlink($cacheFilePath);

            return [];
        }

        $cacheData = base64_decode($cacheData['content']);
        $cacheData = (array) json_decode($cacheData,true);

        return $cacheData;
    }

    /**
     * Check Is Cached Data Valid
     *
     * @param array $cacheData Cached Data
     *
     * @return bool Is Cached Data Valid
     */
    private function _validateCache(array $cacheData = []) : bool
    {
        return isset($cacheData['time']) &&
               intval($cacheData['time']) > time() &&
               isset($cacheData['content']);
    }
}
?>