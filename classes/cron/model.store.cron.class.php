<?php

use Core\Plugins\Database\Exceptions\DatabasePluginException;

class CronStore extends ModelStoreCore
{
    const CRON_TABLE = 'cron_jobs';

    /**
     * @var string|null
     */
    public ?string $scope = 'cron';

    /**
     * @param array|null $values
     * @param int|null $idCron
     *
     * @return bool
     *
     * @throws DatabasePluginException
     */
    final public function updateCronById(
        ?array $values = null,
        ?int   $idCron = null
    ): bool
    {
        if (empty($values)) {
            return false;
        }

        if (empty($idCron)) {
            return false;
        }

        return $this->updateRowById(static::CRON_TABLE, $values, $idCron);
    }

    /**
     * @return array|null
     *
     * @throws DatabasePluginException
     */
    final public function getAllCronRows(): ?array
    {
        $sql = '
            SELECT *
            FROM %s
            ORDER BY id ASC;
        ';

        $sql = sprintf($sql, static::CRON_TABLE);

        return $this->getRows($sql);
    }

    /**
     * @return array|null
     *
     * @throws DatabasePluginException
     */
    final public function getJobs(): ?array
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
