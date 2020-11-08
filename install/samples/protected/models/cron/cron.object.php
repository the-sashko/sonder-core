<?php
class CronObject extends ModelObjectCore
{
    const CRON_TABLE = 'cron_jobs';

    public $scope = 'cron';

    public function createCron(?array $values = null): bool
    {
        if (empty($values)) {
            return false;
        }

        return $this->addRow(static::CRON_TABLE, $values);
    }

    public function updateCronById(
        ?array $values = null,
        ?int   $id     = null
    ): bool
    {
        if (empty($values)) {
            return false;
        }

        if (empty($id)) {
            return false;
        }

        return $this->updateRowById(static::CRON_TABLE, $values, $id);
    }

    public function getAllCrons(): ?array
    {
        $sql = '
            SELECT *
            FROM %s;
        ';

        $sql = sprintf($sql, static::CRON_TABLE);

        return $this->getRows($sql);
    }

    public function getJobs(): ?array
    {
        $sql = '
            SELECT *
            FROM %s
            WHERE
                time_next_exec <= %d AND
                is_active = true;
        ';

        $sql = sprintf($sql, static::CRON_TABLE, time());

        return $this->getRows($sql);
    }
}
