<?php

namespace Sonder\Models\Cron;

use Exception;
use Sonder\Core\Interfaces\IModelStore;
use Sonder\Core\ModelStore;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

final class CronStore extends ModelStore implements IModelStore
{
    const CRON_JOBS_TABLE = 'cron_jobs';

    /**
     * @var string|null
     */
    public ?string $scope = 'cron';

    /**
     * @param int|null $id
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getCronJobRowById(?int $id = null): ?array
    {
        if (empty($id)) {
            return null;
        }

        $sqlWhere = sprintf('"id" = \'%d\'', $id);

        $sql = '
            SELECT *
            FROM "%s"
            WHERE %s
            LIMIT 1;
        ';

        $sql = sprintf($sql, CronStore::CRON_JOBS_TABLE, $sqlWhere);

        return $this->getRow($sql);
    }

    /**
     * @param string|null $alias
     * @param int|null $excludeId
     * @return int|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getCronJobIdRowByAlias(
        ?string $alias = null,
        ?int    $excludeId = null
    ): ?int
    {
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
     * @param string|null $action
     * @param int|null $interval
     * @param int|null $excludeId
     * @return int|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getCronJobIdRowByControllerAndActionAndInterval(
        ?string $controller = null,
        ?string $action = null,
        ?int    $interval = null,
        ?int    $excludeId = null
    ): ?int
    {
        if (empty($controller) || empty($action) || empty($interval)) {
            return null;
        }

        $sqlWhere = sprintf(
            '
                "controller" = \'%s\' AND
                "action" = \'%s\' AND
                "interval" = %d
            ',
            $controller,
            $action,
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
     * @param int $itemsOnPage
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getCronJobRowsByPage(
        int $page = 1,
        int $itemsOnPage = 10
    ): ?array
    {
        $offset = $itemsOnPage * ($page - 1);

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
            $itemsOnPage,
            $offset
        );

        return $this->getRows($sql);
    }

    /**
     * @return int
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
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
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getCronJobRowsForRunning(): ?array
    {
        $sql = '
            SELECT *
            FROM "%s"
            WHERE 
                "time_next_exec" >= %d AND
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
     * @throws DatabasePluginException
     */
    final public function updateCronJobById(
        ?array $row = null,
        ?int   $id = null
    ): bool
    {
        if (empty($row) || empty($id)) {
            return false;
        }

        return $this->updateRowById(CronStore::CRON_JOBS_TABLE, $row, $id);
    }

    /**
     * @param int|null $id
     * @param bool $isSoftDelete
     * @return bool
     * @throws DatabasePluginException
     */
    final public function deleteCronJobById(
        ?int $id = null,
        bool $isSoftDelete = true
    ): bool
    {
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
     * @throws DatabasePluginException
     */
    final public function restoreCronJobById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        $row = [
            'ddate' => NULL,
            'is_active' => true
        ];

        return $this->updateCronJobById($row, $id);
    }

    /**
     * @param array|null $row
     * @return bool
     * @throws DatabasePluginException
     */
    final public function insertCronJob(?array $row = null): bool
    {
        if (empty($row)) {
            return false;
        }

        return $this->addRow(CronStore::CRON_JOBS_TABLE, $row);
    }

    /**
     * @param CronValuesObject|null $cronVO
     * @return bool
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function insertOrUpdateCronJob(
        ?CronValuesObject $cronVO = null
    ): bool
    {
        $id = $cronVO->getId();

        if (empty($id)) {
            $cronVO->setCdate();

            return $this->insertCronJob($cronVO->exportRow());
        }

        $cronVO->setMdate();

        return $this->updateCronJobById($cronVO->exportRow(), $id);
    }
}
