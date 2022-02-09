<?php

namespace Sonder\Models;

use Exception;
use Sonder\Core\CoreModel;
use Sonder\Core\Interfaces\ICron;
use Sonder\Core\Interfaces\IModel;
use Sonder\Core\ValuesObject;
use Sonder\Models\Cron\CronForm;
use Sonder\Models\Cron\CronStore;
use Sonder\Models\Cron\CronValuesObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;
use Throwable;

/**
 * @property CronStore $store
 */
final class Cron extends CoreModel implements IModel, ICron
{
    /**
     * @var int
     */
    protected int $itemsOnPage = 100;

    /**
     * @param int|null $id
     * @return ValuesObject|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function getVOById(?int $id = null): ?ValuesObject
    {
        $row = $this->store->getCronJobRowById($id);

        if (!empty($row)) {
            return $this->getVO($row);
        }

        return null;
    }

    /**
     * @param int $page
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function getCronJobsByPage(int $page): ?array
    {
        $rows = $this->store->getCronJobRowsByPage($page, $this->itemsOnPage);

        if (empty($rows)) {
            return null;
        }

        return $this->getVOArray($rows);
    }

    /**
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function getJobsForRunning(): ?array
    {
        $jobs = $this->store->getCronJobRowsForRunning();

        return $this->getVOArray($jobs);
    }

    /**
     * @return int
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getCronJobsPageCount(): int
    {
        $rowsCount = $this->store->getCronJobRowsCount();

        $pageCount = (int)($rowsCount / $this->itemsOnPage);

        if ($pageCount * $this->itemsOnPage < $rowsCount) {
            $pageCount++;
        }

        return $pageCount;
    }

    /**
     * @param CronForm $cronForm
     * @return bool
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function save(CronForm $cronForm): bool
    {
        $cronForm->checkInputValues();

        if (!$cronForm->getStatus()) {
            return false;
        }

        $this->_checkIdInCronForm($cronForm);
        $this->_checkActionAndIntervalInCronForm($cronForm);

        if (!$cronForm->getStatus()) {
            return false;
        }

        $cronVO = $this->_getVOFromForm($cronForm, true);

        try {
            if (!$this->store->insertOrUpdateCronJob($cronVO)) {
                $cronForm->setStatusFail();

                return false;
            }

            if (!empty($cronForm->getId())) {
                return true;
            }

            $id = $this->store->getCronJobIdRowByActionAndInterval(
                $cronVO->getAction(),
                $cronVO->getInterval()
            );

            if (!empty($id)) {
                $cronForm->setId($id);
            }
        } catch (Throwable $exp) {
            $cronForm->setStatusFail();
            $cronForm->setError($exp->getMessage());

            return false;
        }

        return true;
    }

    /**
     * @param CronValuesObject|null $cronVO
     * @return bool
     * @throws DatabasePluginException
     */
    final public function updateByVO(?CronValuesObject $cronVO = null): bool
    {
        if (empty($cronVO)) {
            return false;
        }

        return $this->store->updateCronJobById(
            $cronVO->exportRow(),
            $cronVO->getId()
        );
    }

    /**
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function removeCronJobById(?int $id): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->deleteCronJobById($id);
    }

    /**
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function restoreCronJobById(?int $id): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->restoreCronJobById($id);
    }

    /**
     * @param CronForm $cronForm
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _checkIdInCronForm(CronForm $cronForm): void
    {
        $id = $cronForm->getId();

        if (!empty($id) && empty($this->_getVOFromForm($cronForm))) {
            $cronForm->setStatusFail();

            $cronForm->setError(sprintf(
                CronForm::CRON_JON_IS_NOT_EXISTS_ERROR_MESSAGE,
                $id
            ));
        }

    }

    /**
     * @param CronForm $cronForm
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _checkActionAndIntervalInCronForm(CronForm $cronForm): void
    {
        if (!empty($this->store->getCronJobIdRowByActionAndInterval(
            $cronForm->getAction(),
            $cronForm->getInterval(),
            $cronForm->getId()
        ))) {
            $cronForm->setStatusFail();

            $cronForm->setError(
                CronForm::ACTION_AND_INTERVAL_EXISTS_ERROR_MESSAGE
            );
        }
    }

    /**
     * @param CronForm $cronForm
     * @param bool $isCreateVOIfEmptyId
     * @return CronValuesObject|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _getVOFromForm(
        CronForm $cronForm,
        bool     $isCreateVOIfEmptyId = false
    ): ?CronValuesObject
    {
        $row = null;

        $id = $cronForm->getId();

        if (empty($id) && !$isCreateVOIfEmptyId) {
            return null;
        }

        if (!empty($id)) {
            $row = $this->store->getCronJobRowById($id);
        }

        if (!empty($id) && empty($row)) {
            return null;
        }

        $cronVO = new CronValuesObject($row);

        $cronVO->setAction($cronForm->getAction());
        $cronVO->setInterval($cronForm->getInterval());
        $cronVO->setIsActive($cronForm->getIsActive());

        return $cronVO;
    }
}
