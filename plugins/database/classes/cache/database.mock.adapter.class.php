<?php
namespace Core\Plugins\Database\Classes;

use Core\Plugins\Database\Interfaces\IDataBaseCacheAdapter;

/**
 * Mock Of Data Base Cache Provider
 */
class DatabaseMockCacheAdapter implements IDataBaseCacheAdapter
{
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
        string $sql,
        array  $data,
        string $scope,
        int    $ttl
    ): bool
    {
        return true;
    }

    /**
     * Get Cached Data From File
     *
     * @param string $sql   SQL Query
     * @param string $scope Scope Of Data Base Request
     *
     * @return array|null Cached Data
     */
    public function get(string $sql, string $scope): ?array
    {
        return null;
    }

    /**
     * Remove All Cache Files Of Data Base Request Scope
     *
     * @param string $scope Scope Of Data Base Request
     *
     * @return bool Is Successfully Removed Cached Data
     */
    public function clean(string $scope): bool
    {
        return true;
    }
}
