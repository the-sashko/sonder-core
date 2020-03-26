<?php
/**
 * Class For Basic Data Base Cache Functions: Read, Write Or Remove Cache
 */
class DBCache
{
    /**
     * @var object Instance OF Data Base Cache Provider
     */
    private $_provider = null;

    public function __construct(string $provider)
    {
        $error = null;

        if (empty($provider)) {
            throw new Exception('DB Cache Provider Is Not Set');
        }

        switch ($provider) {
            case 'redis':
                $this->_provider = new DBRedisCacheProvider();
                break;

            case 'memcache':
                $this->_provider = new DBMemcacheCacheProvider();
                break;

            case 'file':
                $this->_provider = new DBFileCacheProvider();
                break;

            case 'mock':
                $this->_provider = new DBMockCacheProvider();
                break;

            default:
                $error = 'DB Cache Provider Is Not Allowed';

                break;
        }

        if (null !== $error) {
            throw new Exception($error);
        }
    }

    /**
     * Save Cached Data To Provider
     *
     * @param ?string $sql   SQL Query
     * @param ?array  $data  Data Base Data
     * @param ?string $scope Scope Of Data Base Request
     * @param int     $ttl   Time To Live Of Cache
     *
     * @return bool Is Successfully Saved Cached Data
     */
    public function set(
        ?string $sql   = nulll,
        ?array  $data  = null,
        ?string $scope = null,
        int     $ttl   = -1
    ): bool
    {
        if (empty($sql)) {
            return false;
        }

        if (empty($data)) {
            return false;
        }

        if ($ttl < 1) {
            return false;
        }

        $scope = empty($scope) ? 'default' : $scope;

        return $this->_provider->set($sql, $data, $scope, $ttl);
    }

    /**
     * Get Cached Data From Provider
     *
     * @param ?string $sql   SQL Query
     * @param ?string $scope Scope Of Data Base Request
     * @param int     $ttl   Time To Live Of Cache
     *
     * @return array Cached Data
     */
    public function get(
        ?string $sql   = null,
        ?string $scope = null,
        int     $ttl   = -1
    ): ?array
    {
        if (empty($sql)) {
            return null;
        }

        if ($ttl < 1) {
            return null;
        }

        $scope = empty($scope) ? 'default' : $scope;

        $data = $this->_provider->get($sql, $scope);

        if (empty($data)) {
            return null;
        }

        $this->set($sql, $data, $scope, $ttl);

        return $data;
    }

    /**
     * Remove From Provider All Cached Data Of Data Base Request Scope
     *
     * @param ?string $scope Scope Of Data Base Request
     *
     * @return bool Is Successfully Removed Cached Data
     */
    public function flush(?string $scope = null): bool
    {
        $scope = empty($scope) ? 'default' : $scope;

        return $this->_provider->flush($scope);
    }
}
