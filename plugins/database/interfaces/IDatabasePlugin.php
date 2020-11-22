<?php
namespace Core\Plugins\Database\Interfaces;

interface IDataBasePlugin
{
    public function connect(?array $configData = null): void;

    public function select(
        ?string $sql   = null,
        ?string $scope = null,
        ?int    $ttl   = null
    ): ?array;

    public function query(?string $sql = null, ?string $scope = null): bool;

    public function transactionStart(): bool;

    public function transactionCommit(): bool;

    public function transactionRollback(): bool;
}
