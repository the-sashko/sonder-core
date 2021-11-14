<?php

namespace Sonder\Models;

use Exception;
use Sonder\Core\CoreModel;
use Sonder\Core\Interfaces\IModel;

final class Cron extends CoreModel implements IModel
{
    /**
     * @return array|null
     *
     * @throws Exception
     */
    final public function getAll(): ?array
    {
        $rows = $this->store->getAllRows();

        return $this->getVOArray($rows);
    }

    /**
     * @return array|null
     *
     * @throws Exception
     */
    final public function getJobs(): ?array
    {
        $jobs = $this->store->getJobRows();

        return $this->getVOArray($jobs);
    }

    final public function updateByVO(?CronValuesObject $cronVO = null): bool
    {
        if (empty($cronVO)) {
            return false;
        }

        $row = [
            'action' => $cronVO->getAction(),
            'interval' => $cronVO->getInterval(),
            'time_next_exec' => $cronVO->getTimeNextExec(),
            'last_exec_status' => $cronVO->getLastExecStatus() ? 't' : 'f',
            'is_active' => $cronVO->getIsActive() ? 't' : 'f',
            'error_message' => (string)$cronVO->getErrorMessage()
        ];

        return $this->store->updateCronById($row, $cronVO->getId());
    }
}
