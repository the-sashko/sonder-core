<?php

class CronValuesObject extends ValuesObject
{
    const STATUS_WAITING = 'waiting';
    const STATUS_FAILED = 'fail';
    const STATUS_SUCCESS = 'success';

    public function getId(): int
    {
        return (int)$this->get('id');
    }

    public function getAction(): string
    {
        return (string)$this->get('action');
    }

    public function getInterval(): int
    {
        return (int)$this->get('interval');
    }

    public function getIntervalFormatted(): ?string
    {
        $intervalInSeconds = $this->getInterval();

        if (empty($intervalInSeconds)) {
            return null;
        }

        $intervalInMinutes = intdiv($intervalInSeconds, 60);
        $intervalInHours = intdiv($intervalInMinutes, 60);
        $intervalInDays = intdiv($intervalInHours,24);

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
            '%s %s %s',
            (string) $intervalInDays,
            (string) $intervalInHours,
            (string) $intervalInMinutes,
            (string) $intervalInSeconds
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

    public function getTimeNextExec(): int
    {
        return (int)$this->get('time_next_exec');
    }

    public function getTimeNextExecFormatted(): ?string
    {
        $getTimeNextExec = $this->getTimeNextExec();

        if (empty($getTimeNextExec)) {
            return null;
        }

        return date('Y-m-d H:i:s', $getTimeNextExec);
    }

    public function getLastExecStatus(): bool
    {
        return (bool)$this->get('last_exec_status');
    }

    public function getErrorMessage(): ?string
    {
        $lastExecStatus = (bool)$this->getLastExecStatus();
        $isActive = (bool)$this->getIsActive();

        if (!$lastExecStatus && $isActive) {
            return (string)$this->get('error_message');
        }

        return null;
    }

    public function getStatus(): string
    {
        $lastExecStatus = $this->getLastExecStatus();
        $isActive = $this->getIsActive();

        if (!$isActive) {
            return static::STATUS_WAITING;
        }

        if (!$lastExecStatus) {
            return static::STATUS_FAILED;
        }

        return static::STATUS_SUCCESS;
    }

    public function getIsActive(): bool
    {
        return (bool)$this->get('is_active');
    }

    public function setAction(?string $action = null): void
    {
        $this->set('action', $action);
    }

    public function setInterval(?int $interval = null): void
    {
        $this->set('interval', (int)$interval);
    }

    public function setTimeNextExec(): void
    {
        $timeNextExec = time() + $this->getInterval();

        $this->set('time_next_exec', $timeNextExec);
    }

    public function setLastExecStatus(bool $status = false): void
    {
        $this->set('last_exec_status', $status);
    }

    public function setIsActive(bool $isActive = false): void
    {
        $this->set('is_active', $isActive);
    }

    public function setErrorMessage(?string $errorMessage = null): bool
    {
        $errorMessage = (string)$errorMessage;

        if (empty(trim($errorMessage))) {
            $this->set('error_message', null);
            return false;
        }

        if (strlen($errorMessage) > 255) {
            $errorMessage = mb_substr($errorMessage, 0, 254);
            $errorMessage = sprintf('%s…', $errorMessage);
        }

        $this->set('error_message', $errorMessage);
        return true;
    }
}
