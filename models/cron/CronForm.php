<?php

namespace Sonder\Models\Cron;

use Exception;
use Sonder\Core\ModelFormObject;

final class CronForm extends ModelFormObject
{
    const ALIAS_MIN_LENGTH = 3;

    const ALIAS_MAX_LENGTH = 128;

    const CONTROLLER_MIN_LENGTH = 3;

    const CONTROLLER_MAX_LENGTH = 128;

    const ACTION_MIN_LENGTH = 3;

    const ACTION_MAX_LENGTH = 128;

    const ALIAS_EMPTY_ERROR_MESSAGE = 'Alias is empty';

    const ALIAS_TOO_SHORT_ERROR_MESSAGE = 'Alias is too short';

    const ALIAS_TOO_LONG_ERROR_MESSAGE = 'Alias is too long';

    const ALIAS_EXISTS_ERROR_MESSAGE = 'Cron job with this alias already ' .
    'exists';

    const CONTROLLER_EMPTY_ERROR_MESSAGE = 'Controller is empty';

    const CONTROLLER_TOO_SHORT_ERROR_MESSAGE = 'Controller is too short';

    const CONTROLLER_TOO_LONG_ERROR_MESSAGE = 'Controller is too long';

    const ACTION_EMPTY_ERROR_MESSAGE = 'Action is empty';

    const ACTION_TOO_SHORT_ERROR_MESSAGE = 'Action is too short';

    const ACTION_TOO_LONG_ERROR_MESSAGE = 'Action is too long';

    const INTERVAL_EMPTY_ERROR_MESSAGE = 'Time interval is empty';

    const CRON_JOB_NOT_EXISTS_ERROR_MESSAGE = 'Cron job with id "%d" not ' .
    'exists';

    const CRON_JOB_IS_NOT_UNIQUE = 'Cron job is not unique';

    /**
     * @return void
     * @throws Exception
     */
    final public function checkInputValues(): void
    {
        $this->setStatusSuccess();

        $this->_validateAliasValue();
        $this->_validateControllerValue();
        $this->_validateActionValue();
        $this->_validateIntervalValue();
    }

    /**
     * @return int|null
     * @throws Exception
     */
    final public function getId(): ?int
    {
        if (!$this->has('id')) {
            return null;
        }

        $id = $this->get('id');

        if (empty($id)) {
            return null;
        }

        return (int)$id;
    }

    /**
     * @return string|null
     * @throws Exception
     */
    final public function getAlias(): ?string
    {
        if ($this->has('alias')) {
            return $this->get('alias');
        }

        return null;
    }

    /**
     * @return string|null
     * @throws Exception
     */
    final public function getController(): ?string
    {
        if ($this->has('controller')) {
            return $this->get('controller');
        }

        return null;
    }

    /**
     * @return string|null
     * @throws Exception
     */
    final public function getAction(): ?string
    {
        if ($this->has('action')) {
            return $this->get('action');
        }

        return null;
    }

    /**
     * @return int|null
     * @throws Exception
     */
    final public function getInterval(): ?int
    {
        if ($this->has('interval')) {
            return $this->get('interval');
        }

        return null;
    }

    /**
     * @return bool
     * @throws Exception
     */
    final public function getIsActive(): bool
    {
        if (!$this->has('is_active')) {
            return false;
        }

        return (bool)$this->get('is_active');
    }

    /**
     * @param int|null $id
     * @return void
     * @throws Exception
     */
    final public function setId(?int $id = null): void
    {
        $this->set('id', $id);
    }

    /**
     * @param string|null $alias
     * @return void
     * @throws Exception
     */
    final public function setAlias(?string $alias = null): void
    {
        $this->set('alias', $alias);
    }

    /**
     * @param string|null $controller
     * @return void
     * @throws Exception
     */
    final public function setController(?string $controller = null): void
    {
        $this->set('controller', $controller);
    }

    /**
     * @param string|null $action
     * @return void
     * @throws Exception
     */
    final public function setAction(?string $action = null): void
    {
        $this->set('action', $action);
    }

    /**
     * @param int|null $interval
     * @return void
     * @throws Exception
     */
    final public function setInterval(?int $interval = null): void
    {
        $this->set('interval', $interval);
    }

    /**
     * @param bool $isActive
     * @return void
     * @throws Exception
     */
    final public function setIsActive(bool $isActive = false): void
    {
        $this->set('is_active', $isActive);
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function _validateAliasValue(): void
    {
        $alias = $this->getAlias();

        if (empty($alias)) {
            $this->setError(CronForm::ALIAS_EMPTY_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (!empty($alias) && mb_strlen($alias) > CronForm::ALIAS_MAX_LENGTH) {
            $this->setError(CronForm::ALIAS_TOO_LONG_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (!empty($alias) && mb_strlen($alias) < CronForm::ALIAS_MIN_LENGTH) {
            $this->setError(CronForm::ALIAS_TOO_SHORT_ERROR_MESSAGE);
            $this->setStatusFail();
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function _validateControllerValue(): void
    {
        $controller = $this->getController();

        if (empty($controller)) {
            $this->setError(CronForm::CONTROLLER_EMPTY_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (
            !empty($controller) &&
            mb_strlen($controller) > CronForm::CONTROLLER_MAX_LENGTH
        ) {
            $this->setError(CronForm::CONTROLLER_TOO_LONG_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (
            !empty($controller) &&
            mb_strlen($controller) < CronForm::CONTROLLER_MIN_LENGTH
        ) {
            $this->setError(CronForm::CONTROLLER_TOO_SHORT_ERROR_MESSAGE);
            $this->setStatusFail();
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function _validateActionValue(): void
    {
        $action = $this->getAction();

        if (empty($action)) {
            $this->setError(CronForm::ACTION_EMPTY_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (!empty($action) && mb_strlen($action) > CronForm::ACTION_MAX_LENGTH) {
            $this->setError(CronForm::ACTION_TOO_LONG_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (!empty($action) && mb_strlen($action) < CronForm::ACTION_MIN_LENGTH) {
            $this->setError(CronForm::ACTION_TOO_SHORT_ERROR_MESSAGE);
            $this->setStatusFail();
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function _validateIntervalValue(): void
    {
        $interval = $this->getInterval();
        $interval = empty($interval) ? 0 : $interval;
        $interval = $interval < 1 ? 0 : $interval;

        $this->setInterval($interval);

        if (empty($interval)) {
            $this->setError(CronForm::INTERVAL_EMPTY_ERROR_MESSAGE);
            $this->setStatusFail();
        }
    }
}
