<?php
namespace Core\Plugins\Database\Interfaces;

interface IDatabaseCache
{
    public function set(
        ?string $sql   = null,
        ?array  $data  = null,
        ?string $scope = null,
        ?int    $ttl   = null
    ): bool;

    public function get(?string $sql = null, ?string $scope = null): ?array;

    public function clean(?string $scope = null): bool;
}
