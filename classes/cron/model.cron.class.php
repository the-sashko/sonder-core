<?php
class Cron extends ModelCore
{
    public function __construct()
    {
        parent::__construct();

        $databaseConfig = $this->configData['database'];

        $this->store             = new CronStore($databaseConfig);
        $this->valuesObjectClass = 'CronValuesObject';
    }

    public function getAll(): ?array
    {
        $crons = $this->store->getAllCrons();

        return $this->getVOArray($crons);
    }

    public function getJobs(): ?array
    {
        $jobs = $this->store->getJobs();

        return $this->getVOArray($jobs);
    }

    public function updateByVO(?CronValuesObject $cronVO = null): bool
    {
        if (empty($cronVO)) {
            return false;
        }

        $row = [
            'action'           => $cronVO->getAction(),
            'interval'         => $cronVO->getInterval(),
            'time_next_exec'   => $cronVO->getTimeNextExec(),
            'last_exec_status' => $cronVO->getLastExecStatus() ? 't' : 'f',
            'is_active'        => $cronVO->getIsActive() ? 't' : 'f',
            'error_message'    => (string) $cronVO->getErrorMessage()
        ];

        return $this->store->updateCronById($row, $cronVO->getId());
    }
}
