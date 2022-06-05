<?php

namespace Sonder\Models;

use Exception;
use ReflectionException;
use ReflectionMethod;
use Sonder\Core\CoreModel;
use Sonder\Core\Interfaces\ICron;
use Sonder\Core\Interfaces\IModel;
use Sonder\Core\RequestObject;
use Sonder\Core\ValuesObject;
use Sonder\Models\Cron\CronForm;
use Sonder\Models\Cron\CronStore;
use Sonder\Models\Cron\CronValuesObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;
use Sonder\Plugins\LoggerPlugin;
use Throwable;

/**
 * @property CronStore $store
 */
final class Cron extends CoreModel implements IModel, ICron
{
    const JOB_METHOD_NAME_PATTERN = '/^job(.*?)$/su';

    /**
     * @var int
     */
    protected int $itemsOnPage = 100;

    /**
     * @param CronValuesObject|null $cronVO
     * @return bool
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function runJob(?CronValuesObject $cronVO = null): bool
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

        $cronVO->setStatus(CronValuesObject::STATUS_RUNNING);
        $this->updateByVO($cronVO);

        try {
            $controllerClassName = $this->_getControllerClassNameFromVO(
                $cronVO
            );

            $methodName = $this->_getMethodNameFromVO($cronVO);

            $controller = new $controllerClassName(new RequestObject());

            $controller->$methodName();

            $cronVO->setTimeNextExec();
            $cronVO->setStatus(CronValuesObject::STATUS_SCHEDULED);
        } catch (Throwable $thr) {
            $cronVO->setStatus(CronValuesObject::STATUS_ERROR);
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
    final public function getCronJobsByPage(
        int $page
    ): ?array
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
                $cronVO->getMethod(),
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
     * @param CronValuesObject|null $cronVO
     * @return bool
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function updateByVO(?CronValuesObject $cronVO = null): bool
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
     * @throws DatabasePluginException
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
     * @throws DatabasePluginException
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
                CronForm::CRON_JOB_NOT_EXISTS_ERROR_MESSAGE,
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
    private function _checkAliasInCronForm(CronForm $cronForm): void
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
     * @param CronForm $cronForm
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _checkIsCronJobInFormUnique(CronForm $cronForm): void
    {
        $id = $this->store->getCronJobIdRowByControllerAndMethodAndInterval(
            $cronForm->getController(),
            $cronForm->getMethod(),
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

        $cronVO->setAlias($cronForm->getAlias());
        $cronVO->setController($cronForm->getController());
        $cronVO->setMethod($cronForm->getMethod());
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
    ): bool
    {
        $reflection = new ReflectionMethod($controllerClassName, $methodName);

        if (!$reflection->isPublic()) {
            return false;
        }

        if (!preg_match(
            Cron::JOB_METHOD_NAME_PATTERN,
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
            Cron::JOB_METHOD_NAME_PATTERN,
            '$1',
            $methodName
        );

        $methodName = lcfirst($methodName);

        $methodName = preg_replace(
            '/([A-Z])/su',
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
    ): string
    {
        $controllerName = preg_replace(
            '/^(.*?)\/([a-z]+)Controller\.php$/sui',
            '$2',
            $controllerFilePath
        );

        $controllerName = lcfirst($controllerName);

        $controllerName = preg_replace(
            '/([A-Z])/su',
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
        array  &$controllers,
        string $dirPath
    ): void
    {
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
    ): ?string
    {
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
     * @param CronValuesObject $cronVO
     * @return string
     * @throws Exception
     */
    private function _getControllerClassNameFromVO(
        CronValuesObject $cronVO
    ): string
    {
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
     * @param CronValuesObject $cronVO
     * @return string
     * @throws Exception
     */
    private function _getMethodNameFromVO(CronValuesObject $cronVO): string
    {
        $methodName = $cronVO->getMethod();

        $methodName = array_map(function ($methodNamePart) {
            return mb_convert_case($methodNamePart, MB_CASE_TITLE);
        }, explode('_', $methodName));

        $methodName = lcfirst(implode('', $methodName));

        return sprintf('job%s', $methodName);
    }
}
