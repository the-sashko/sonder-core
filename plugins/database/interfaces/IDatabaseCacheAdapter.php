<?php
namespace Core\Plugins\Database\Interfaces;

interface IDatabaseCacheAdapter
{
    public function set(
        string $sql,
        array  $data,
        string $scope,
        int    $ttl
    ): bool;

    public function get(string $sql, string $scope): ?array;

    public function clean(string $scope): bool;
}
