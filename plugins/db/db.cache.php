<?php
/**
 * Class For Basic Data Base Cache Functions: Read, Write Or Remove Cache
 */
class DBCache
{
    /**
     * @var object Instance OF Data Base Cache Provider
     */
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

    /**
     * Save Cached Data To Provider
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

    /**
     * Get Cached Data From Provider
     *
     * @param string $sql   SQL Query
     * @param string $scope Scope Of Data Base Request
     *
     * @return array Cached Data
     */
    public function get(
        string $sql   = '',
        string $scope = 'default',
        int    $ttl   = -1
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

    /**
     * Remove From Provider All Cached Data Of Data Base Request Scope
     *
     * @param string $scope Scope Of Data Base Request
     *
     * @return bool Is Successfully Removed Cached Data
     */
    public function flush(string $scope = 'default') : bool
    {
        $scope = strlen($scope) < 1 ? 'default' : $scope;

        return $this->provider->flush($scope);
    }
}
?>