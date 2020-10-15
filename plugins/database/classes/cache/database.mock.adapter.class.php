<?php
namespace Core\Plugins\Database\Classes;

use Core\Plugins\Database\Interfaces\IDataBaseCacheAdapter;

/**
 * Mock For Data Base Cahe Provider
 */
class DatabaseMockCacheAdapter implements IDataBaseCacheAdapter
{
    /**
     * Mock Of Saving Data To Cache
     *
     * @param string $sql   SQL Query
     * @param array  $data  Data Base Data
     * @param string $scope Scope Of Data Base Request
     * @param int    $ttl   Time To Live Of Cache
     *
     * @return bool Is Successfully Saved Cached Data (Always Returns true)
     */
    public function set(
        string $sql,
        array  $data,
        string $scope,
        int    $ttl
    ): bool
    {
        return true;
    }

    /**
     * Mock Of Get Data From Cache
     *
     * @param string $sql   SQL Query
     * @param string $scope Scope Of Data Base Request
     *
     * @return array Cached Data (Always Returns Empty Array)
     */
    public function get(string $sql, string $scope): ?array
    {
        return null;
    }

    /**
     * Mock Of Removing All Cached Data Of Data Base Request Scope
     *
     * @param string $scope Scope Of Data Base Request
     *
     * @return bool Is Successfully Removed Cached Data (Always Returns true)
     */
    public function clean(string $scope): bool
    {
        return true;
    }
}
