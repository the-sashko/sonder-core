<?php
interface DBCacheProvider
{
    public function set(
        string $sql = '',
        array $data = [],
        string $scope = 'default',
        int $ttl = -1
    ) : bool;

    public function get(
        string $sql = '',
        string $scope = 'default'
    ) : array;

    public function flush(string $scope = 'default') : bool;
}
?>