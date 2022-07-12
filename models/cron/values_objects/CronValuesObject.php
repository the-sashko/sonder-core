<?php

namespace Sonder\Models\Cron\ValuesObjects;

use Sonder\Core\ModelValuesObject;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\ICronValuesObject;
use Sonder\Interfaces\IModelSimpleValuesObject;
use Sonder\Interfaces\IModelValuesObject;
use Sonder\Interfaces\IValuesObject;
use Sonder\Models\Cron\Enums\CronStatusesEnum;
use Sonder\Models\Cron\Interfaces\ICronStatusesEnum;
use Sonder\Models\Cron\Interfaces\ICronValuesObject as ICronModelValuesObject;

#[IValuesObject]
#[IModelSimpleValuesObject]
#[IModelValuesObject]
#[ICronValuesObject]
#[ICronModelValuesObject]
final class CronValuesObject
    extends ModelValuesObject
    implements ICronValuesObject, ICronModelValuesObject
{
    final protected const EDIT_LINK_PATTERN = '/admin/settings/cron/job/%d/';

    final protected const REMOVE_LINK_PATTERN = '/admin/settings/cron/job/remove/%d/';

    final protected const RESTORE_LINK_PATTERN = '/admin/settings/cron/job/restore/%d/';

    final protected const ADMIN_VIEW_LINK_PATTERN = '/admin/settings/cron/job/view/%d/';

    final protected const ADMIN_RUN_LINK_PATTERN = '/admin/settings/cron/job/run/%d/';

    /**
     * @return string
     * @throws ValuesObjectException
     */
    final public function getAlias(): string
    {
        return (string)$this->get('alias');
    }

    /**
     * @return string
     * @throws ValuesObjectException
     */
    final public function getController(): string
    {
        return (string)$this->get('controller');
    }

    /**
     * @return string
     * @throws ValuesObjectException
     */
    final public function getControllerMethod(): string
    {
        return (string)$this->get('controller_method');
    }

    /**
     * @param bool $isFormatAsString
     * @return string|int|null
     * @throws ValuesObjectException
     */
    final public function getInterval(
        bool $isFormatAsString = false
    ): string|int|null {
        $intervalInSeconds = $this->get('interval');

        if (empty($intervalInSeconds)) {
            return null;
        }

        if (!$isFormatAsString) {
            return (int)$intervalInSeconds;
        }

        $intervalInMinutes = intdiv($intervalInSeconds, 60);
        $intervalInHours = intdiv($intervalInMinutes, 60);
        $intervalInDays = intdiv($intervalInHours, 24);

        $intervalInSeconds = $intervalInSeconds - 60 * $intervalInMinutes;
        $intervalInMinutes = $intervalInMinutes - 60 * $intervalInHours;
        $intervalInHours = $intervalInHours - 24 * $intervalInDays;

        $intervalInSeconds = $intervalInSeconds > 0 ? $intervalInSeconds : null;
        $intervalInMinutes = $intervalInMinutes > 0 ? $intervalInMinutes : null;
        $intervalInHours = $intervalInHours > 0 ? $intervalInHours : null;
        $intervalInDays = $intervalInDays > 0 ? $intervalInDays : null;

        if (!empty($intervalInSeconds)) {
            $intervalInSeconds = sprintf(
                '%d second(s)',
                $intervalInSeconds
            );
        }

        if (!empty($intervalInMinutes)) {
            $intervalInMinutes = sprintf(
                '%d minute(s)',
                $intervalInMinutes
            );
        }

        if (!empty($intervalInHours)) {
            $intervalInHours = sprintf('%d hour(s)', $intervalInHours);
        }

        if (!empty($intervalInDays)) {
            $intervalInDays = sprintf('%d day(s)', $intervalInDays);
        }

        $intervalFormatted = sprintf(
            '%s %s %s %s',
            $intervalInDays,
            $intervalInHours,
            $intervalInMinutes,
            $intervalInSeconds
        );

        $intervalFormatted = preg_replace(
            '/(\s+)/u',
            ' ',
            $intervalFormatted
        );

        $intervalFormatted = preg_replace(
            '/((^\s)|(\s$))/u',
            '',
            $intervalFormatted
        );

        if (empty($intervalFormatted)) {
            return null;
        }

        return $intervalFormatted;
    }

    /**
     * @param string|null $format
     * @return string|int|null
     * @throws ValuesObjectException
     */
    final public function getTimeNextExec(
        ?string $format = null
    ): string|int|null {
        $timeNextExec = $this->get('time_next_exec');

        if (empty($timeNextExec)) {
            return null;
        }

        if (empty($format)) {
            return (int)$timeNextExec;
        }

        return date('Y-m-d H:i:s', $timeNextExec);
    }

    /**
     * @return string
     * @throws ValuesObjectException
     */
    final public function getStatus(): string
    {
        $status = null;

        if ($this->has('status')) {
            /* @var $status ICronStatusesEnum|null */
            $status = $this->get('status');
        }

        if (empty($status)) {
            return CronStatusesEnum::DEFAULT->value;
        }

        return $status->value;
    }

    /**
     * @return string|null
     * @throws ValuesObjectException
     */
    final public function getErrorMessage(): ?string
    {
        $errorMessage = $this->get('error_message');

        if (empty($errorMessage)) {
            return null;
        }

        return $errorMessage;
    }

    /**
     * @return string
     * @throws ValuesObjectException
     */
    final public function getAdminViewLink(): string
    {
        return sprintf(
            CronValuesObject::ADMIN_VIEW_LINK_PATTERN,
            $this->getId()
        );
    }

    /**
     * @return string
     * @throws ValuesObjectException
     */
    final public function getAdminRunLink(): string
    {
        return sprintf(
            CronValuesObject::ADMIN_RUN_LINK_PATTERN,
            $this->getId()
        );
    }

    /**
     * @param string|null $alias
     * @return void
     * @throws ValuesObjectException
     */
    final public function setAlias(?string $alias = null): void
    {
        if (!empty($alias)) {
            $this->set('alias', $alias);
        }
    }

    /**
     * @param string|null $controller
     * @return void
     * @throws ValuesObjectException
     */
    final public function setController(?string $controller = null): void
    {
        if (!empty($controller)) {
            $this->set('controller', $controller);
        }
    }

    /**
     * @param string|null $method
     * @return void
     * @throws ValuesObjectException
     */
    final public function setControllerMethod(?string $method = null): void
    {
        if (!empty($method)) {
            $this->set('controller_method', $method);
        }
    }

    /**
     * @param int|null $interval
     * @return void
     * @throws ValuesObjectException
     */
    final public function setInterval(?int $interval = null): void
    {
        if (!empty($interval)) {
            $this->set('interval', $interval);
        }
    }

    /**
     * @return void
     * @throws ValuesObjectException
     */
    final public function setTimeNextExec(): void
    {
        $interval = $this->getInterval();
        $interval = empty($interval) ? 0 : $interval;

        $timeNextExec = time() + $interval;

        $this->set('time_next_exec', $timeNextExec);
    }

    /**
     * @param string|null $status
     * @return void
     * @throws ValuesObjectException
     */
    final public function setStatus(?string $status = null): void
    {
        if (!empty($status) || !CronStatusesEnum::tryFrom($status)) {
            $status = CronStatusesEnum::DEFAULT->value;
        }

        $status = CronStatusesEnum::from($status);

        $this->set('status', $status->value);
    }

    /**
     * @param string|null $errorMessage
     * @return void
     * @throws ValuesObjectException
     */
    final public function setErrorMessage(?string $errorMessage = null): void
    {
        $errorMessage = empty($errorMessage) ? null : $errorMessage;

        $this->set('error_message', $errorMessage);
    }
}
