<?php
class CronObject extends ModelObjectCore
{
    const CRON_TABLE = 'cron_jobs';

    public $scope = 'cron';

    public function create(?array $values = null): bool
    {
        if (empty($values)) {
            return false;
        }

        $columns = array_keys($values);
        $columns = implode(', ', $columns);
        $values  = implode(', ', $values);

        $sql = '
            INSERT INTO %s (
                %s
            ) VALUES (
                %s
            );
        ';

        $sql = sprintf($sql, static::CRON_TABLE, $columns, $values);

        return $this->insert($sql);
    }

    public function updateCronByID(
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

        foreach ($values as $key => $value) {
            $values[$key] = sprintf('%s = %s', $key, $values);
        }

        $values = implode(', ', $values);

        $sql = '
            UPDATE %s
            SET %s
            WHERE id = %d;
        ';

        $sql = sprintf($sql, static::CRON_TABLE, $values, $id)

        return $this->query($sql);
    }

    public function getAllCrons(): array
    {
        $sql = '
            SELECT *
            FROM %s;
        ';

        $sql = sprintf($sql, static::CRON_TABLE);

        return $this->getRows($sql);
    }

    public function getJobs(): array
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
