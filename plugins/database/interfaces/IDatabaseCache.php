<?php
namespace Sonder\Plugins\Database\Interfaces;

interface IDatabaseCache
{
    /**
     * @param string|null $sql
     * @param array|null $data
     * @param string|null $scope
     * @param int|null $ttl
     *
     * @return bool
     */
    public function set(
        ?string $sql   = null,
        ?array  $data  = null,
        ?string $scope = null,
        ?int    $ttl   = null
    ): bool;

    /**
     * @param string|null $sql
     * @param string|null $scope
     *
     * @return array|null
     */
    public function get(?string $sql = null, ?string $scope = null): ?array;

    /**
     * @param string|null $scope
     *
     * @return bool
     */
    public function clean(?string $scope = null): bool;
}
