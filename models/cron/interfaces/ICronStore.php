<?php

namespace Sonder\Models\Cron\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelStore;

#[IModelStore]
#[Attribute(Attribute::TARGET_CLASS)]
interface ICronStore extends IModelStore
{
    /**
     * @param int|null $id
     * @return array|null
     */
    public function getCronJobRowById(?int $id = null): ?array;

    /**
     * @param string|null $alias
     * @param int|null $excludeId
     * @return int|null
     */
    public function getCronJobIdRowByAlias(
        ?string $alias = null,
        ?int $excludeId = null
    ): ?int;

    /**
     * @param string|null $controller
     * @param string|null $method
     * @param int|null $interval
     * @param int|null $excludeId
     * @return int|null
     */
    public function getCronJobIdRowByControllerAndMethodAndInterval(
        ?string $controller = null,
        ?string $method = null,
        ?int $interval = null,
        ?int $excludeId = null
    ): ?int;

    /**
     * @param int $page
     * @param int $limit
     * @return array|null
     */
    public function getCronJobRowsByPage(
        int $page = 1,
        int $limit = 10
    ): ?array;

    /**
     * @return int
     */
    public function getCronJobRowsCount(): int;

    /**
     * @return array|null
     */
    public function getCronJobRowsForRunning(): ?array;

    /**
     * @param array|null $row
     * @param int|null $id
     * @return bool
     */
    public function updateCronJobById(
        ?array $row = null,
        ?int $id = null
    ): bool;

    /**
     * @param int|null $id
     * @param bool $isSoftDelete
     * @return bool
     */
    public function deleteCronJobById(
        ?int $id = null,
        bool $isSoftDelete = true
    ): bool;

    /**
     * @param int|null $id
     * @return bool
     */
    public function restoreCronJobById(?int $id = null): bool;

    /**
     * @param array|null $row
     * @return bool
     */
    public function insertCronJob(?array $row = null): bool;

    /**
     * @param ICronValuesObject|null $cronVO
     * @return bool
     */
    public function insertOrUpdateCronJob(
        ?ICronValuesObject $cronVO = null
    ): bool;
}
