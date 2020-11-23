<?php
class Cron extends ModelCore
{
    public function __construct()
    {
        parent::__construct();

        $databaseConfig = $this->getConfig('database');

        $this->object            = new CronObject($databaseConfig);
        $this->valuesObjectClass = 'CronValuesObject';
    }

    public function getAll(): ?array
    {
        $crons = $this->object->getAllCrons();

        return $this->getVOArray($crons);
    }

    public function getJobs(): ?array
    {
        $jobs = $this->object->getJobs();

        return $this->getVOArray($jobs);
    }

    public function updateByVO(?CronValuesObject $cronVO = null): bool
    {
        if (empty($cronVO)) {
            return false;
        }

        $values = [
            'action'           => $cronVO->getAction(),
            'interval'         => $cronVO->getInterval(),
            'time_next_exec'   => $cronVO->getTimeNextExec(),
            'last_exec_status' => $cronVO->getLastExecStatus(),
            'is_active'        => $cronVO->getIsActive(),
            'error_message'    => $cronVO->getErrorMessage()
        ];

        return $this->object->updateCronById($values, $cronVO->getId());
    }
}
