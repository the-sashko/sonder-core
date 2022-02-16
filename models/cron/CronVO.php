<?php

namespace Sonder\Models\Cron;

use Exception;
use Sonder\Core\Interfaces\ICronValuesObject;
use Sonder\Core\ModelValuesObject;

final class CronValuesObject
    extends ModelValuesObject
    implements ICronValuesObject
{
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_RUNNING = 'running';
    const STATUS_ERROR = 'error';
    const STATUS_UNKNOWN = 'unknown';

    /**
     * @var string|null
     */
    protected ?string $editLinkPattern = '/admin/settings/cron/job/%d/';

    /**
     * @var string|null
     */
    protected ?string $removeLinkPattern = '/admin/settings/cron/job/remove' .
    '/%d/';

    /**
     * @var string|null
     */
    protected ?string $restoreLinkPattern = '/admin/settings/cron/job' .
    '/restore/%d/';

    /**
     * @var string|null
     */
    protected ?string $adminViewLinkPattern = '/admin/settings/cron/job/view' .
    '/%d/';

    /**
     * @var string|null
     */
    protected ?string $adminRunLinkPattern = '/admin/settings/cron/job/run/%d/';

    /**
     * @return string
     * @throws Exception
     */
    final public function getAlias(): string
    {
        return (string)$this->get('alias');
    }

    /**
     * @return string
     * @throws Exception
     */
    final public function getController(): string
    {
        return (string)$this->get('controller');
    }

    /**
     * @return string
     * @throws Exception
     */
    final public function getMethod(): string
    {
        return (string)$this->get('method');
    }

    /**
     * @param bool $isFormatAsString
     * @return string|int|null
     * @throws Exception
     */
    final public function getInterval(
        bool $isFormatAsString = false
    ): string|int|null
    {
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
            '/(\s+)/su',
            ' ',
            $intervalFormatted
        );

        $intervalFormatted = preg_replace(
            '/((^\s)|(\s$))/su',
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
     * @throws Exception
     */
    final public function getTimeNextExec(
        ?string $format = null
    ): string|int|null
    {
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
     * @throws Exception
     */
    final public function getStatus(): string
    {
        $status = $this->get('status');

        if (empty($status)) {
            return CronValuesObject::STATUS_UNKNOWN;
        }

        return $status;
    }

    /**
     * @return string|null
     * @throws Exception
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
     * @throws Exception
     */
    final public function getAdminViewLink(): string
    {
        return sprintf($this->adminViewLinkPattern, $this->getId());
    }

    /**
     * @return string
     * @throws Exception
     */
    final public function getAdminRunLink(): string
    {
        return sprintf($this->adminRunLinkPattern, $this->getId());
    }

    /**
     * @param string|null $alias
     * @return void
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
     */
    final public function setMethod(?string $method = null): void
    {
        if (!empty($method)) {
            $this->set('method', $method);
        }
    }

    /**
     * @param int|null $interval
     * @return void
     * @throws Exception
     */
    final public function setInterval(?int $interval = null): void
    {
        if (!empty($interval)) {
            $this->set('interval', $interval);
        }
    }

    /**
     * @return void
     * @throws Exception
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
     * @throws Exception
     */
    final public function setStatus(?string $status = null): void
    {
        if (!empty($status)) {
            $this->set('status', $status);
        }
    }

    /**
     * @param string|null $errorMessage
     * @return void
     * @throws Exception
     */
    final public function setErrorMessage(?string $errorMessage = null): void
    {
        $errorMessage = empty($errorMessage) ? null : $errorMessage;

        $this->set('error_message', $errorMessage);
    }
}
