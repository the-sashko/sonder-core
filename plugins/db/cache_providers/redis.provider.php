<?php
class DBRedisCacheProvider
{
    const REDIS_DBCACHE_KEY_PREFIX = 'cache:db';

    public $redis = NULL;

    public function __construct() {
        $this->redis = new RedisPlugin();
        $this->redis->setKeyPrefix(static::REDIS_DBCACHE_KEY_PREFIX);
    }

    public function set(
        string $sql = '',
        array $data = [],
        string $scope = 'default',
        int $ttl = -1
    ) : bool
    {
        $redisKey = $scope.':'.md5($sql);

        $data = json_encode($data);
        $data = base64_encode($data);

        $res = $this->redis->set('query_list:'.$redisKey, $sql, $ttl);
        return $res && $this->redis->set('data_list:'.$redisKey, $data, $ttl);
    }

    public function get(
        string $sql = '',
        string $scope = 'default'
    ) : array
    {
        $redisKey = $scope.':'.md5($sql);

        $res = $this->redis->get('query_list:'.$redisKey);

        if (!strlen($res) > 0) {
            return [];
        }

        if ($sql != $res) {
            return [];
        }

        $res = $this->redis->get('data_list:'.$redisKey);

        $res = base64_decode($res);
        return (array) json_decode($res, true);
    }

    public function flush(string $scope = 'default') : bool
    {
        $res = $this->redis->delByPattern('query_list:'.$scope.':*');
        return $res && $this->redis->delByPattern('data_list:'.$scope.':*');
    }
}
?>