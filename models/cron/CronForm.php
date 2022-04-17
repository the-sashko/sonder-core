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

    const METHOD_MIN_LENGTH = 3;

    const METHOD_MAX_LENGTH = 128;

    const ALIAS_EMPTY_ERROR_MESSAGE = 'Alias is empty';

    const ALIAS_TOO_SHORT_ERROR_MESSAGE = 'Alias is too short';

    const ALIAS_TOO_LONG_ERROR_MESSAGE = 'Alias is too long';

    const ALIAS_EXISTS_ERROR_MESSAGE = 'Cron job with this alias already ' .
    'exists';

    const CONTROLLER_EMPTY_ERROR_MESSAGE = 'Controller is empty';

    const CONTROLLER_TOO_SHORT_ERROR_MESSAGE = 'Controller is too short';

    const CONTROLLER_TOO_LONG_ERROR_MESSAGE = 'Controller is too long';

    const METHOD_EMPTY_ERROR_MESSAGE = 'Method is empty';

    const METHOD_TOO_SHORT_ERROR_MESSAGE = 'Method is too short';

    const METHOD_TOO_LONG_ERROR_MESSAGE = 'Method is too long';

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
        $this->_validateMethodValue();
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
    final public function getMethod(): ?string
    {
        if ($this->has('method')) {
            return $this->get('method');
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
    final public function isActive(): bool
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
     * @param string|null $method
     * @return void
     * @throws Exception
     */
    final public function setMethod(?string $method = null): void
    {
        $this->set('$method', $method);
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
    private function _validateAliasValue(): void
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
    private function _validateControllerValue(): void
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
    private function _validateMethodValue(): void
    {
        $method = $this->getMethod();

        if (empty($method)) {
            $this->setError(CronForm::METHOD_EMPTY_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (
            !empty($method) &&
            mb_strlen($method) > CronForm::METHOD_MAX_LENGTH
        ) {
            $this->setError(CronForm::METHOD_TOO_LONG_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (
            !empty($method) &&
            mb_strlen($method) < CronForm::METHOD_MIN_LENGTH) {
            $this->setError(CronForm::METHOD_TOO_SHORT_ERROR_MESSAGE);
            $this->setStatusFail();
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private function _validateIntervalValue(): void
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
