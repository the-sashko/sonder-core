<?php
/**
 * Data Base Cahe Provider For Memcache
 */
class DBMemcacheCacheProvider
{
    public function set(
        string $sql = '',
        array $data = [],
        string $scope = 'default',
        int $ttl = -1
    ) : bool
    {
        // To-Do
        throw new Exception('Not Implemented');
    }

    public function get(
        string $sql = '',
        string $scope = 'default'
    ) : array
    {
        // To-Do
        throw new Exception('Not Implemented');
    }

    public function flush(string $scope = 'default') : bool
    {
        // To-Do
        throw new Exception('Not Implemented');
    }
}
?>