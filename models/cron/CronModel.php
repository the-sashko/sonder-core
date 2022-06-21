<?php

namespace Sonder\Models;

use ReflectionException;
use ReflectionMethod;
use Sonder\Core\CoreModel;
use Sonder\Exceptions\CoreException;
use Sonder\Exceptions\ModelException;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\ICronModel as ICronModelFramework;
use Sonder\Interfaces\ICronValuesObject;
use Sonder\Interfaces\IModel;
use Sonder\Core\RequestObject;
use Sonder\Models\Cron\ValuesObjects\CronValuesObject;
use Sonder\Models\Cron\Enums\CronStatusesEnum;
use Sonder\Models\Cron\Forms\CronForm;
use Sonder\Models\Cron\Interfaces\ICronForm;
use Sonder\Models\Cron\Interfaces\ICronModel;
use Sonder\Models\Cron\Interfaces\ICronStore;
use Sonder\Models\Cron\Interfaces\ICronValuesObject as ICronModelValuesObject;
use Sonder\Plugins\LoggerPlugin;
use Throwable;

/**
 * @property ICronStore $store
 */
#[IModel]
#[ICronModelFramework]
#[ICronModel]
final class CronModel
    extends CoreModel
    implements ICronModel, ICronModelFramework
{
    const ITEMS_ON_PAGE = 100;

    private const JOB_METHOD_NAME_PATTERN = '/^job(.*?)$/su';

    /**
     * @param ICronValuesObject|null $cronVO
     * @return bool
     * @throws ValuesObjectException
     * @throws CoreException
     */
    final public function runJob(?ICronValuesObject $cronVO = null): bool
    {
        if (empty($cronVO)) {
            return false;
        }

        $status = true;

        echo sprintf(
            "[%s] Start \"%s\" Job\n",
            date('Y-m-d H:i:s'),
            $cronVO->getAlias()
        );

        /* @var $loggerPlugin LoggerPlugin */
        $loggerPlugin = $this->getPlugin('logger');

        $loggerPlugin->log(
            'Start Cron Job',
            mb_convert_case($cronVO->getAlias(), MB_CASE_LOWER),
            'cron'
        );

        $cronVO->setStatus(CronStatusesEnum::RUNNING->value);
        $this->updateByVO($cronVO);

        try {
            $controllerClassName = $this->_getControllerClassNameFromVO(
                $cronVO
            );

            $controllerMethod = $this->_getControllerMethodFromVO($cronVO);

            $controller = new $controllerClassName(new RequestObject());

            $controller->$controllerMethod();

            $cronVO->setTimeNextExec();
            $cronVO->setStatus(CronStatusesEnum::SCHEDULED->value);
        } catch (Throwable $thr) {
            $cronVO->setStatus(CronStatusesEnum::ERROR->value);
            $cronVO->setErrorMessage($thr->getMessage());

            $logErrorMessage = sprintf(
                'Cron Job Error: %s',
                $thr->getMessage()
            );

            $logName = sprintf(
                'cron_%s',
                mb_convert_case($cronVO->getAlias(), MB_CASE_LOWER)
            );

            $loggerPlugin->log(
                $logErrorMessage,
                mb_convert_case($cronVO->getAlias(), MB_CASE_LOWER),
                'cron'
            );

            $loggerPlugin->logError($logErrorMessage, $logName);

            echo sprintf(
                "[%s] Error \"%s\"\n",
                date('Y-m-d H:i:s'),
                $thr->getMessage()
            );

            $status = false;
        }

        $this->updateByVO($cronVO);

        $loggerPlugin->log(
            'End Cron Job',
            mb_convert_case($cronVO->getAlias(), MB_CASE_LOWER),
            'cron'
        );

        echo sprintf(
            "[%s] End \"%s\" Job\n\n",
            date('Y-m-d H:i:s'),
            $cronVO->getAlias()
        );

        return $status;
    }

    /**
     * @param int|null $id
     * @return ICronValuesObject|null
     * @throws ModelException
     */
    final public function getVOById(?int $id = null): ?ICronValuesObject
    {
        $row = $this->store->getCronJobRowById($id);

        if (!empty($row)) {
            /* @var $cronVO ?ICronModelValuesObject */
            $cronVO = $this->getVO($row);

            return $cronVO;
        }

        return null;
    }

    /**
     * @param int $page
     * @return array|null
     * @throws ModelException
     */
    final public function getCronJobsByPage(
        int $page
    ): ?array {
        $rows = $this->store->getCronJobRowsByPage(
            $page,
            CronModel::ITEMS_ON_PAGE
        );

        if (empty($rows)) {
            return null;
        }

        return $this->getVOArray($rows);
    }

    /**
     * @return array|null
     * @throws ModelException
     */
    final public function getJobsForRunning(): ?array
    {
        $jobs = $this->store->getCronJobRowsForRunning();

        return $this->getVOArray($jobs);
    }

    /**
     * @return int
     */
    final public function getCronJobsPageCount(): int
    {
        $rowsCount = $this->store->getCronJobRowsCount();

        $pageCount = (int)($rowsCount / CronModel::ITEMS_ON_PAGE);

        if ($pageCount * CronModel::ITEMS_ON_PAGE < $rowsCount) {
            $pageCount++;
        }

        return $pageCount;
    }

    /**
     * @param ICronForm $cronForm
     * @return bool
     * @throws ValuesObjectException
     */
    final public function save(ICronForm $cronForm): bool
    {
        $cronForm->checkInputValues();

        if (!$cronForm->getStatus()) {
            return false;
        }

        $this->_checkIdInCronForm($cronForm);
        $this->_checkAliasInCronForm($cronForm);
        $this->_checkIsCronJobInFormUnique($cronForm);

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

            $id = $this->store->getCronJobIdRowByControllerAndMethodAndInterval(
                $cronVO->getController(),
                $cronVO->getControllerMethod(),
                $cronVO->getInterval()
            );

            if (!empty($id)) {
                $cronForm->setId($id);
            }
        } catch (Throwable $thr) {
            $cronForm->setStatusFail();
            $cronForm->setError($thr->getMessage());

            return false;
        }

        return true;
    }

    /**
     * @param ICronValuesObject|null $cronVO
     * @return bool
     * @throws ValuesObjectException
     */
    final public function updateByVO(?ICronValuesObject $cronVO = null): bool
    {
        if (empty($cronVO)) {
            return false;
        }

        $cronVO->setMdate();

        return $this->store->updateCronJobById(
            $cronVO->exportRow(),
            $cronVO->getId()
        );
    }

    /**
     * @param int|null $id
     * @return bool
     */
    final public function removeCronJobById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->deleteCronJobById($id);
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

        return $this->store->restoreCronJobById($id);
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    final public function getAvailableJobs(): array
    {
        $jobs = [];

        $controllers = $this->_getAvailableControllers();

        foreach ($controllers as $controllerName => $controllerClassName) {
            $methods = $this->_getAvailableMethods($controllerClassName);

            if (!empty($methods)) {
                $jobs[$controllerName] = $methods;
            }
        }

        return $jobs;
    }

    /**
     * @param ICronForm $cronForm
     * @return void
     * @throws ValuesObjectException
     */
    private function _checkIdInCronForm(ICronForm $cronForm): void
    {
        $id = $cronForm->getId();

        if (!empty($id) && empty($this->_getVOFromForm($cronForm))) {
            $cronForm->setStatusFail();

            $cronForm->setError(
                sprintf(
                    CronForm::CRON_JOB_NOT_EXISTS_ERROR_MESSAGE,
                    $id
                )
            );
        }
    }

    /**
     * @param ICronForm $cronForm
     * @return void
     */
    private function _checkAliasInCronForm(ICronForm $cronForm): void
    {
        $id = $this->store->getCronJobIdRowByAlias(
            $cronForm->getAlias(),
            $cronForm->getId()
        );

        if (!empty($id)) {
            $cronForm->setStatusFail();

            $cronForm->setError(
                CronForm::ALIAS_EXISTS_ERROR_MESSAGE
            );
        }
    }

    /**
     * @param ICronForm $cronForm
     * @return void
     */
    private function _checkIsCronJobInFormUnique(ICronForm $cronForm): void
    {
        $id = $this->store->getCronJobIdRowByControllerAndMethodAndInterval(
            $cronForm->getController(),
            $cronForm->getControllerMethod(),
            $cronForm->getInterval(),
            $cronForm->getId()
        );

        if (!empty($id)) {
            $cronForm->setStatusFail();

            $cronForm->setError(
                CronForm::CRON_JOB_IS_NOT_UNIQUE
            );
        }
    }

    /**
     * @param ICronForm $cronForm
     * @param bool $isCreateVOIfEmptyId
     * @return ICronValuesObject|null
     * @throws ValuesObjectException
     */
    private function _getVOFromForm(
        ICronForm $cronForm,
        bool $isCreateVOIfEmptyId = false
    ): ?ICronValuesObject {
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

        $cronVO->setAlias($cronForm->getAlias());
        $cronVO->setController($cronForm->getController());
        $cronVO->setControllerMethod($cronForm->getControllerMethod());
        $cronVO->setInterval($cronForm->getInterval());
        $cronVO->setIsActive($cronForm->isActive());

        return $cronVO;
    }

    /**
     * @return array
     */
    private function _getAvailableControllers(): array
    {
        $controllers = [];

        $controllersPaths = $this->_getControllersPaths();

        foreach ($controllersPaths as $controllersDirPath) {
            $this->_setControllersFromDirPath(
                $controllers,
                $controllersDirPath
            );
        }

        return $controllers;
    }

    /**
     * @param string $controllerClassName
     * @return array
     * @throws ReflectionException
     */
    private function _getAvailableMethods(string $controllerClassName): array
    {
        $methods = get_class_methods($controllerClassName);

        foreach ($methods as $methodKey => $methodName) {
            if (!$this->_isMethodValid($controllerClassName, $methodName)) {
                unset($methods[$methodKey]);

                continue;
            }

            $methods[$methodKey] = $this->_getFormattedMethodName($methodName);
        }

        return $methods;
    }

    /**
     * @param string $controllerClassName
     * @param string $methodName
     * @return bool
     * @throws ReflectionException
     */
    private function _isMethodValid(
        string $controllerClassName,
        string $methodName
    ): bool {
        $reflection = new ReflectionMethod($controllerClassName, $methodName);

        if (!$reflection->isPublic()) {
            return false;
        }

        if (!preg_match(
            CronModel::JOB_METHOD_NAME_PATTERN,
            $methodName
        )) {
            return false;
        }

        return true;
    }

    /**
     * @param string $methodName
     * @return string
     */
    private function _getFormattedMethodName(string $methodName): string
    {
        $methodName = preg_replace(
            CronModel::JOB_METHOD_NAME_PATTERN,
            '$1',
            $methodName
        );

        $methodName = lcfirst($methodName);

        $methodName = preg_replace(
            '/([A-Z])/u',
            '_$1',
            $methodName
        );

        return mb_convert_case($methodName, MB_CASE_LOWER);
    }

    /**
     * @return string[]
     */
    private function _getControllersPaths(): array
    {
        $controllersPaths = [
            APP_PROTECTED_DIR_PATH . '/controllers',
            APP_FRAMEWORK_DIR_PATH . '/controllers'
        ];

        if (
            array_key_exists('controllers', APP_SOURCE_PATHS) &&
            is_array(APP_SOURCE_PATHS['controllers'])
        ) {
            $controllersPaths = APP_SOURCE_PATHS['controllers'];
        }

        return $controllersPaths;
    }

    /**
     * @param string $controllerFilePath
     * @return string
     */
    private function _getControllerNameFromFilePath(
        string $controllerFilePath
    ): string {
        $controllerName = preg_replace(
            '/^(.*?)\/([a-z]+)Controller\.php$/sui',
            '$2',
            $controllerFilePath
        );

        $controllerName = lcfirst($controllerName);

        $controllerName = preg_replace(
            '/([A-Z])/u',
            '_$1',
            $controllerName
        );

        return mb_convert_case($controllerName, MB_CASE_LOWER);
    }

    /**
     * @param array $controllers
     * @param string $dirPath
     * @return void
     */
    private function _setControllersFromDirPath(
        array &$controllers,
        string $dirPath
    ): void {
        $filePathPattern = sprintf('%s/*Controller.php', $dirPath);

        foreach ((array)glob($filePathPattern) as $controllerFilePath) {
            $controllerName = $this->_getControllerNameFromFilePath(
                $controllerFilePath
            );

            if (array_key_exists($controllerName, $controllers)) {
                continue;
            }

            $controllerClassName = $this->_getControllerClassNameByFilePath(
                $controllerFilePath
            );

            if (empty($controllerClassName)) {
                continue;
            }

            $controllers[$controllerName] = $controllerClassName;
        }
    }

    /**
     * @param string $controllerFilePath
     * @return string|null
     */
    private function _getControllerClassNameByFilePath(
        string $controllerFilePath
    ): ?string {
        $controllerName = preg_replace(
            '/^(.*?)\/([a-z]+)Controller\.php$/sui',
            '$2',
            $controllerFilePath
        );

        $controllerClassName = sprintf(
            'Sonder\Controllers\%sController',
            $controllerName
        );

        require_once $controllerFilePath;

        if (!class_exists($controllerClassName)) {
            return null;
        }

        return $controllerClassName;
    }

    /**
     * @param ICronValuesObject $cronVO
     * @return string
     */
    private function _getControllerClassNameFromVO(
        ICronValuesObject $cronVO
    ): string {
        $controllerClassName = $cronVO->getController();

        $controllerClassName = array_map(function ($controllerClassNamePart) {
            return mb_convert_case($controllerClassNamePart, MB_CASE_TITLE);
        }, explode('_', $controllerClassName));

        $controllerClassName = implode('', $controllerClassName);

        return sprintf(
            'Sonder\Controllers\%sController',
            $controllerClassName
        );
    }

    /**
     * @param ICronValuesObject $cronVO
     * @return string
     */
    private function _getControllerMethodFromVO(
        ICronValuesObject $cronVO
    ): string {
        $controllerMethod = $cronVO->getControllerMethod();

        $controllerMethod = array_map(function ($controllerMethodPart) {
            return mb_convert_case($controllerMethodPart, MB_CASE_TITLE);
        }, explode('_', $controllerMethod));

        $controllerMethod = lcfirst(implode('', $controllerMethod));

        return sprintf('job%s', $controllerMethod);
    }
}
