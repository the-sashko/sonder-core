<?php

namespace Sonder\Models\Cron;

use Sonder\Exceptions\ValuesObjectException;
use Sonder\Core\ModelStore;
use Sonder\Interfaces\IModelStore;
use Sonder\Models\Cron\Interfaces\ICronStore;
use Sonder\Models\Cron\Interfaces\ICronValuesObject;

#[IModelStore]
#[ICronStore]
final class CronStore extends ModelStore implements ICronStore
{
    final protected const SCOPE ='cron';

    private const CRON_JOBS_TABLE = 'cron_jobs';

    /**
     * @param int|null $id
     * @return array|null
     */
    final public function getCronJobRowById(?int $id = null): ?array
    {
        return $this->getRowById(self::CRON_JOBS_TABLE, $id);
    }

    /**
     * @param string|null $alias
     * @param int|null $excludeId
     * @return int|null
     */
    final public function getCronJobIdRowByAlias(
        ?string $alias = null,
        ?int $excludeId = null
    ): ?int {
        if (empty($alias)) {
            return null;
        }

        $sqlWhere = sprintf('"alias" = \'%s\'', $alias);

        if (!empty($excludeId)) {
            $sqlWhere = sprintf(
                '%s AND "id" <> %d',
                $sqlWhere,
                $excludeId
            );
        }

        $sql = '
            SELECT "id"
            FROM "%s"
            WHERE %s
            LIMIT 1;
        ';

        $sql = sprintf($sql, CronStore::CRON_JOBS_TABLE, $sqlWhere);

        $id = $this->getOne($sql);

        return empty($id) ? null : (int)$id;
    }

    /**
     * @param string|null $controller
     * @param string|null $method
     * @param int|null $interval
     * @param int|null $excludeId
     * @return int|null
     */
    final public function getCronJobIdRowByControllerAndMethodAndInterval(
        ?string $controller = null,
        ?string $method = null,
        ?int $interval = null,
        ?int $excludeId = null
    ): ?int {
        if (empty($controller) || empty($method) || empty($interval)) {
            return null;
        }

        $sqlWhere = sprintf(
            '
                "controller" = \'%s\' AND
                "controller_method" = \'%s\' AND
                "interval" = %d
            ',
            $controller,
            $method,
            $interval
        );

        if (!empty($excludeId)) {
            $sqlWhere = sprintf(
                '%s AND "id" <> %d',
                $sqlWhere,
                $excludeId
            );
        }

        $sql = '
            SELECT "id"
            FROM "%s"
            WHERE %s
            LIMIT 1;
        ';

        $sql = sprintf($sql, CronStore::CRON_JOBS_TABLE, $sqlWhere);

        $id = $this->getOne($sql);

        return empty($id) ? null : (int)$id;
    }

    /**
     * @param int $page
     * @param int $limit
     * @return array|null
     */
    final public function getCronJobRowsByPage(
        int $page = 1,
        int $limit = 10
    ): ?array {
        $offset = $limit * ($page - 1);

        $sql = '
            SELECT *
            FROM "%s"
            ORDER BY "cdate" DESC
            LIMIT %d
            OFFSET %d;
        ';

        $sql = sprintf(
            $sql,
            CronStore::CRON_JOBS_TABLE,
            $limit,
            $offset
        );

        return $this->getRows($sql);
    }

    /**
     * @return int
     */
    final public function getCronJobRowsCount(): int
    {
        $sql = '
            SELECT COUNT("id") AS "count"
            FROM "%s";
        ';

        $sql = sprintf($sql, CronStore::CRON_JOBS_TABLE);

        return (int)$this->getOne($sql);
    }

    /**
     * @return array|null
     */
    final public function getCronJobRowsForRunning(): ?array
    {
        $sql = '
            SELECT *
            FROM "%s"
            WHERE 
                (
                    "time_next_exec" IS NULL OR
                    "time_next_exec" <= %d
                ) AND
                ("ddate" IS NULL OR "ddate" < 1) AND
                "is_active" = true
            ORDER BY "time_next_exec" DESC;
        ';

        $sql = sprintf(
            $sql,
            CronStore::CRON_JOBS_TABLE,
            time()
        );

        return $this->getRows($sql);
    }

    /**
     * @param array|null $row
     * @param int|null $id
     * @return bool
     */
    final public function updateCronJobById(
        ?array $row = null,
        ?int $id = null
    ): bool {
        if (empty($row) || empty($id)) {
            return false;
        }

        return $this->updateRowById(CronStore::CRON_JOBS_TABLE, $row, $id);
    }

    /**
     * @param int|null $id
     * @param bool $isSoftDelete
     * @return bool
     */
    final public function deleteCronJobById(
        ?int $id = null,
        bool $isSoftDelete = true
    ): bool {
        if (empty($id)) {
            return false;
        }

        if ($isSoftDelete) {
            $row = [
                'ddate' => time(),
                'is_active' => false
            ];

            return $this->updateCronJobById($row, $id);
        }

        return $this->deleteRowById(CronStore::CRON_JOBS_TABLE, $id);
    }

    /**
     * @param int|null $id
     * @return bool
     */
    final public function restoreCronJobById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        $row = [
            'ddate' => null,
            'is_active' => true
        ];

        return $this->updateCronJobById($row, $id);
    }

    /**
     * @param array|null $row
     * @return bool
     */
    final public function insertCronJob(?array $row = null): bool
    {
        if (empty($row)) {
            return false;
        }

        return $this->addRow(CronStore::CRON_JOBS_TABLE, $row);
    }

    /**
     * @param ICronValuesObject|null $cronVO
     * @return bool
     * @throws ValuesObjectException
     */
    final public function insertOrUpdateCronJob(
        ?ICronValuesObject $cronVO = null
    ): bool {
        $id = $cronVO->getId();

        if (empty($id)) {
            $cronVO->setCdate();

            return $this->insertCronJob($cronVO->exportRow());
        }

        $cronVO->setMdate();

        return $this->updateCronJobById($cronVO->exportRow(), $id);
    }
}
