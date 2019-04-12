<?php
/**
 * Mock For Data Base Cahe Provider
 */
class DBMockCacheProvider
{
    public function set(
        string $sql = '',
        array $data = [],
        string $scope = 'default',
        int $ttl = -1
    ) : bool
    {
        return true;
    }

    public function get(
        string $sql = '',
        string $scope = 'default'
    ) : array
    {
        return [];
    }

    public function flush(string $scope = 'default') : bool
    {
        return true;
    }
}
?>