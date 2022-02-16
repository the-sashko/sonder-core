<?php

namespace Sonder\Core\Interfaces;

use Sonder\Models\Cron\CronValuesObject;

interface ICron
{
    /**
     * @param CronValuesObject|null $cronVO
     * @return bool
     */
    public function runJob(?CronValuesObject $cronVO = null): bool;

    /**
     * @return array|null
     */
    public function getJobsForRunning(): ?array;
}
