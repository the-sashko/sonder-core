<?php
class DBRedisCacheProvider
{
    const REDIS_DBCACHE_KEY_PREFIX = 'cache:db';

    public $redis        = NULL;
    public $fileProvider = NULL;

    public function __construct() {
        $this->redis = new RedisPlugin();
        $this->redis->setKeyPrefix(static::REDIS_DBCACHE_KEY_PREFIX);

        $this->fileProvider = new DBFileCacheProvider();
    }

    public function set(
        string $sql = '',
        array $data = [],
        string $scope = 'default',
        int $ttl = -1
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

    public function get(
        string $sql = '',
        string $scope = 'default'
    ) : array
    {
        $redisKey = $scope.':'.md5($sql);

        $res = $this->redis->get('query_list:'.$redisKey);

        if (!strlen($res) > 0) {

            return $this->fileProvider->get($sql, $scope);
        }

        if ($sql != $res) {

            return $this->fileProvider->get($sql, $scope);
        }

        $res = $this->redis->get('data_list:'.$redisKey);

        $res = base64_decode($res);
        $res = (array) json_decode($res, true);

        if (count($res) > 0) {

            return $res;
        }

        return $this->fileProvider->get($sql, $scope);
    }

    public function flush(string $scope = 'default') : bool
    {
        $this->fileProvider->flush($scope);

        $res = $this->redis->delByPattern('query_list:'.$scope.':*');

        return $res && $this->redis->delByPattern('data_list:'.$scope.':*');
    }
}
?>