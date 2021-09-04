<?php

use Core\Plugins\Database\Exceptions\DatabasePluginException;

class Cron extends ModelCore
{
    /**
     * @throws DatabasePluginException
     * @throws CoreException
     */
    public function __construct()
    {
        parent::__construct();

        $databaseConfig = $this->configData['database'];

        $this->store = new CronStore($databaseConfig);
        $this->valuesObjectClass = 'CronValuesObject';
    }

    /**
     * @return array|null
     *
     * @throws Exception
     */
    final public function getAll(): ?array
    {
        $cronRows = $this->store->getAllCronRows();

        return $this->getVOArray($cronRows);
    }

    /**
     * @return array|null
     *
     * @throws Exception
     */
    final public function getJobs(): ?array
    {
        $jobs = $this->store->getJobs();

        return $this->getVOArray($jobs);
    }

    /**
     * @param CronValuesObject|null $cronVO
     *
     * @return bool
     */
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
