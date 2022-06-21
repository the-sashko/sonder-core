<?php

namespace Sonder\Models\Cron\Forms;

use Sonder\Core\ModelFormObject;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelFormObject;
use Sonder\Models\Cron\Interfaces\ICronForm;

#[IModelFormObject]
#[ICronForm]
final class CronForm extends ModelFormObject implements ICronForm
{
    final public const ALIAS_EMPTY_ERROR_MESSAGE = 'Alias is empty';

    final public const ALIAS_TOO_SHORT_ERROR_MESSAGE = 'Alias is too short';

    final public const ALIAS_TOO_LONG_ERROR_MESSAGE = 'Alias is too long';

    final public const ALIAS_EXISTS_ERROR_MESSAGE = 'Cron job with this alias already exists';

    final public const CONTROLLER_EMPTY_ERROR_MESSAGE = 'Controller is empty';

    final public const CONTROLLER_TOO_SHORT_ERROR_MESSAGE = 'Controller is too short';

    final public const CONTROLLER_TOO_LONG_ERROR_MESSAGE = 'Controller is too long';

    final public const CONTROLLER_METHOD_EMPTY_ERROR_MESSAGE = 'Controller Method is empty';

    final public const CONTROLLER_METHOD_TOO_SHORT_ERROR_MESSAGE = 'Controller Method is too short';

    final public const CONTROLLER_METHOD_TOO_LONG_ERROR_MESSAGE = 'Controller Method is too long';

    final public const INTERVAL_EMPTY_ERROR_MESSAGE = 'Time interval is empty';

    final public const CRON_JOB_NOT_EXISTS_ERROR_MESSAGE = 'Cron job with id "%d" not exists';

    final public const CRON_JOB_IS_NOT_UNIQUE = 'Cron job is not unique';

    private const ALIAS_MIN_LENGTH = 3;

    private const ALIAS_MAX_LENGTH = 128;

    private const CONTROLLER_MIN_LENGTH = 3;

    private const CONTROLLER_MAX_LENGTH = 128;

    private const CONTROLLER_METHOD_MIN_LENGTH = 3;

    private const CONTROLLER_METHOD_MAX_LENGTH = 128;

    /**
     * @return void
     * @throws ValuesObjectException
     */
    final public function checkInputValues(): void
    {
        $this->setStatusSuccess();

        $this->_validateAliasValue();
        $this->_validateControllerValue();
        $this->_validateControllerMethodValue();
        $this->_validateIntervalValue();
    }

    /**
     * @return int|null
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
     */
    final public function getControllerMethod(): ?string
    {
        if ($this->has('controller_method')) {
            return $this->get('controller_method');
        }

        return null;
    }

    /**
     * @return int|null
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
     */
    final public function setId(?int $id = null): void
    {
        $this->set('id', $id);
    }

    /**
     * @param string|null $alias
     * @return void
     * @throws ValuesObjectException
     */
    final public function setAlias(?string $alias = null): void
    {
        $this->set('alias', $alias);
    }

    /**
     * @param string|null $controller
     * @return void
     * @throws ValuesObjectException
     */
    final public function setController(?string $controller = null): void
    {
        $this->set('controller', $controller);
    }

    /**
     * @param string|null $controllerMethod
     * @return void
     * @throws ValuesObjectException
     */
    final public function setControllerMethod(
        ?string $controllerMethod = null
    ): void {
        $this->set('controller_method', $controllerMethod);
    }

    /**
     * @param int|null $interval
     * @return void
     * @throws ValuesObjectException
     */
    final public function setInterval(?int $interval = null): void
    {
        $this->set('interval', $interval);
    }

    /**
     * @param bool $isActive
     * @return void
     * @throws ValuesObjectException
     */
    final public function setIsActive(bool $isActive = false): void
    {
        $this->set('is_active', $isActive);
    }

    /**
     * @return void
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
     */
    private function _validateControllerMethodValue(): void
    {
        $controllerMethod = $this->getControllerMethod();

        if (empty($controllerMethod)) {
            $this->setError(CronForm::CONTROLLER_METHOD_EMPTY_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (
            !empty($controllerMethod) &&
            mb_strlen(
                $controllerMethod
            ) > CronForm::CONTROLLER_METHOD_MAX_LENGTH
        ) {
            $this->setError(CronForm::CONTROLLER_METHOD_TOO_LONG_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (
            !empty($controllerMethod) &&
            mb_strlen(
                $controllerMethod
            ) < CronForm::CONTROLLER_METHOD_MIN_LENGTH) {
            $this->setError(
                CronForm::CONTROLLER_METHOD_TOO_SHORT_ERROR_MESSAGE
            );
            $this->setStatusFail();
        }
    }

    /**
     * @return void
     * @throws ValuesObjectException
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
