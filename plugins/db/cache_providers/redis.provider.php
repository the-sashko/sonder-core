<?php
/**
 * Data Base Cahe Provider For Redis
 */
class DBRedisCacheProvider
{
    /**
     * @var string Redis Entry Key Prefix
     */
    const REDIS_DBCACHE_KEY_PREFIX = 'cache:db';

    /**
     * @var object Instance Of RedisPlugin
     */
    public $redis = NULL;

    /**
     * @var object Instance Of DBFileCacheProvider
     */
    public $fileProvider = NULL;

    public function __construct() {
        $this->redis = new RedisPlugin();
        $this->redis->setKeyPrefix(static::REDIS_DBCACHE_KEY_PREFIX);

        $this->fileProvider = new DBFileCacheProvider();
    }

    /**
     * Save Cached Data To Redis
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
        $redisKey = $scope.':'.md5($sql);

        $cacheData = json_encode($data);
        $cacheData = base64_encode($cacheData);

        try {
            $res = $this->redis->set('query_list:'.$redisKey, $sql, $ttl);

            return $res && $this->redis->set(
                'data_list:'.$redisKey,
                $cacheData,
                $ttl
            );
        } catch (Exception $exp) {
            (new LoggerPlugin)->logError($exp->getMessage());

            return $this->fileProvider->set($sql, $data, $scope, $ttl);
        }
    }

    /**
     * Get Cached Data From Redis
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
        $redisKey = $scope.':'.md5($sql);

        $res = $this->redis->get('query_list:'.$redisKey);

        if (!strlen($res) > 0) {
            return $this->fileProvider->get($sql, $scope);
        }

        if ($sql !== $res) {
            return $this->fileProvider->get($sql, $scope);
        }

        $res = $this->redis->get('data_list:'.$redisKey);

        $res = base64_decode($res);
        $res = (array) json_decode($res, TRUE);

        if (count($res) > 0) {
            return $res;
        }

        return $this->fileProvider->get($sql, $scope);
    }

    /**
     * Remove From Redis All Cached Data Of Data Base Request Scope
     *
     * @param string $scope Scope Of Data Base Request
     *
     * @return bool Is Successfully Removed Cached Data
     */
    public function flush(string $scope = 'default') : bool
    {
        $this->fileProvider->flush($scope);

        $res = $this->redis->delByPattern('query_list:'.$scope.':*');

        return $res && $this->redis->delByPattern('data_list:'.$scope.':*');
    }
}
?>
