<?php
namespace Sonder\Plugins\Database\Interfaces;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
interface IDatabasePlugin
{
    /**
     * @param array|null $configData
     */
    public function connect(?array $configData = null): void;

    /**
     * @param string|null $sql
     * @param string|null $scope
     * @param int|null $ttl
     *
     * @return array|null
     */
    public function select(
        ?string $sql   = null,
        ?string $scope = null,
        ?int    $ttl   = null
    ): ?array;

    /**
     * @param string|null $sql
     * @param string|null $scope
     *
     * @return bool
     */
    public function query(?string $sql = null, ?string $scope = null): bool;

    /**
     * @return bool
     */
    public function transactionStart(): bool;

    /**
     * @return bool
     */
    public function transactionCommit(): bool;

    /**
     * @return bool
     */
    public function transactionRollback(): bool;
}
