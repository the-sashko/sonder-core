<?php
namespace Sonder\Plugins\Database\Interfaces;

interface IDatabaseCacheAdapter
{
    /**
     * @param string $sql
     * @param array $data
     * @param string $scope
     * @param int $ttl
     *
     * @return bool
     */
    public function set(
        string $sql,
        array  $data,
        string $scope,
        int    $ttl
    ): bool;

    /**
     * @param string $sql
     * @param string $scope
     *
     * @return array|null
     */
    public function get(string $sql, string $scope): ?array;

    /**
     * @param string $scope
     *
     * @return bool
     */
    public function clean(string $scope): bool;
}
