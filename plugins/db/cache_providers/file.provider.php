<?php
/**
 * Data Base Cahe Provider For Files
 */
class DBFileCacheProvider
{
    const DB_CACHE_DIR = __DIR__.'/../../../../res/cache/db';

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    public function set(
        string $sql = '',
        array $data = [],
        string $scope = 'default',
        int $ttl = -1
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
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    public function get(
        string $sql = '',
        string $scope = 'default'
    ) : array
    {
        $cacheFilePath = $this->_getCacheFilePath($sql, $scope);

        return $this->_getDataFromCache($cacheFilePath);
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
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
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
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
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
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
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _validateCache(array $cacheData = []) : bool
    {
        return isset($cacheData['time']) &&
               intval($cacheData['time']) > time() &&
               isset($cacheData['content']);
    }
}
?>
