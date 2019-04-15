<?php
/**
 * Interface For Data Base Cahe Providers
 */
interface DBCacheProvider
{
    /**
     * Save Data To Cache
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
    ) : bool;

    /**
     * Get Data From Cache
     *
     * @param string $sql   SQL Query
     * @param string $scope Scope Of Data Base Request
     *
     * @return array Cached Data
     */
    public function get(
        string $sql   = '',
        string $scope = 'default'
    ) : array;

    /**
     * Remove All Cached Data Of Data Base Request Scope
     *
     * @param string $scope Scope Of Data Base Request
     *
     * @return bool Is Successfully Removed Cached Data
     */
    public function flush(string $scope = 'default') : bool;
}
?>