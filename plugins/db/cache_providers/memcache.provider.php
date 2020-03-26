<?php
/**
 * Data Base Cahe Provider For Memcache
 */
class DBMemcacheCacheProvider
{
    /**
     * Save Cached Data To Memcache
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
    ) : bool
    {
        // To-Do
        throw new Exception('Not Implemented');
    }

    /**
     * Get Cached Data From Memcache
     *
     * @param string $sql   SQL Query
     * @param string $scope Scope Of Data Base Request
     *
     * @return array Cached Data
     */
    public function get(string $sql, string $scope): ?array
    {
        // To-Do
        throw new Exception('Not Implemented');
    }

    /**
     * Remove From Memcache All Cached Data Of Data Base Request Scope
     *
     * @param string $scope Scope Of Data Base Request
     *
     * @return bool Is Successfully Removed Cached Data
     */
    public function flush(string $scope): bool
    {
        // To-Do
        throw new Exception('Not Implemented');
    }
}
