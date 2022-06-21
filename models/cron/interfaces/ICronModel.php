<?php

namespace Sonder\Models\Cron\Interfaces;

use Attribute;
use Sonder\Interfaces\IModel;
use Sonder\Interfaces\ICronValuesObject;

#[IModel]
#[Attribute(Attribute::TARGET_CLASS)]
interface ICronModel extends IModel
{
    /**
     * @param ICronValuesObject|null $cronVO
     * @return bool
     */
    public function runJob(?ICronValuesObject $cronVO = null): bool;

    /**
     * @param int|null $id
     * @return ICronValuesObject|null
     */
    public function getVOById(?int $id = null): ?ICronValuesObject;

    /**
     * @param int $page
     * @return array|null
     */
    public function getCronJobsByPage(int $page): ?array;

    /**
     * @return array|null
     */
    public function getJobsForRunning(): ?array;

    /**
     * @return int
     */
    public function getCronJobsPageCount(): int;

    /**
     * @param ICronForm $cronForm
     * @return bool
     */
    public function save(ICronForm $cronForm): bool;

    /**
     * @param ICronValuesObject|null $cronVO
     * @return bool
     */
    public function updateByVO(?ICronValuesObject $cronVO = null): bool;

    /**
     * @param int|null $id
     * @return bool
     */
    public function removeCronJobById(?int $id = null): bool;

    /**
     * @param int|null $id
     * @return bool
     */
    public function restoreCronJobById(?int $id = null): bool;

    /**
     * @return array
     */
    public function getAvailableJobs(): array;
}
