<?php
/**
 * 
 */
class DBCache
{
    public $provider = NULL;

    public function __construct(string $provider = '')
    {
        $error = NULL;

        switch ($provider) {
            case 'redis':
                $this->provider = new DBRedisCacheProvider();
                break;

            case 'memcache':
                $this->provider = new DBMemcacheCacheProvider();
                break;

            case 'file':
                $this->provider = new DBFileCacheProvider();
                break;

            case 'mock':
                $this->provider = new DBMockCacheProvider();
                break;

            default:
                $error = "DB Cache Provider \"{$provider}\" Is Not Allowed";
                break;
        }

        if ($error != NULL) {
            throw new Exception($error);
        }
    }

    public function set(
        string $sql = '',
        array $data = [],
        string $scope = 'default',
        int $ttl = -1
    ) : bool
    {
        if (strlen($sql) < 12) {
            return false;
        }

        if (count($data) < 1) {
            return false;
        }

        if ($ttl < 1) {
            return false;
        }

        $scope = strlen($scope) < 1 ? 'default' : $scope;

        return $this->provider->set($sql, $data, $scope, $ttl);
    }

    public function get(
        string $sql = '',
        string $scope = 'default',
        int $ttl = -1
    ) : array
    {
        if (strlen($sql) < 12) {
            return [];
        }

        if ($ttl < 1) {
            return [];
        }

        $scope = strlen($scope) < 1 ? 'default' : $scope;

        $data = $this->provider->get($sql, $scope);

        if (count($data) < 1) {
            return [];
        }

        $this->set($sql, $data, $scope, $ttl);

        return $data;
    }

    public function flush(string $scope = 'default') : bool
    {
        $scope = strlen($scope) < 1 ? 'default' : $scope;

        return $this->provider->flush($scope);
    }
}
?>