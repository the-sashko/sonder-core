<?php
class CronVO extends ValuesObject
{
    const STATUS_WAITING = 'waiting';
    const STATUS_FAILED  = 'fail';
    const STATUS_SUCCESS = 'success';

    public function getID() : int
    {
        return (int) $this->get('id');
    }

    public function getAction() : string
    {
        return (string) $this->get('action');
    }

    public function getInterval() : int
    {
        return (int) $this->get('interval');
    }

    public function getTimeNextExec() : int
    {
        return (int) $this->get('time_next_exec');
    }

    public function getLastExecStatus() : int
    {
        return (int) $this->get('last_exec_status');
    }

    public function getErrorMessage() : string
    {
        $lastExecStatus = (bool) $this->getLastExecStatus();
        $isActive = (bool) $this->getIsActive();

        if (!$lastExecStatus && $isActive) {
            return (string) $this->get('error_message');
        }

        return '';
    }

    public function getStatus() : string
    {
        $lastExecStatus = (bool) $this->getLastExecStatus();
        $isActive  = (bool) $this->getIsActive();

        if (!$isActive) {
            return static::STATUS_WAITING;
        }

        if (!$lastExecStatus) {
            return static::STATUS_FAILED;
        }

        return static::STATUS_SUCCESS;
    }

    public function getIsActive() : int
    {
        return (int) $this->get('is_active');
    }

    public function setAction(string $action = '') : void
    {
        $this->set('action', $action);
    }

    public function setInterval(int $interval = -1) : void
    {
        $this->set('interval', $interval);
    }

    public function setTimeNextExec() : void
    {
        $timeNextExec = time() + $this->getInterval();

        $this->set('time_next_exec', $timeNextExec);
    }

    public function setLastExecStatus(int $status = 0) : void
    {
        $this->set('last_exec_status', $status);
    }

    public function setIsActive(int $isActive = 0) : void
    {
        $this->set('is_active', $isActive);
    }

    public function setErrorMessage(string $errorMessage = '') : bool
    {
        if (strlen(trim($errorMessage)) < 1) {
            $this->set('error_message', '');

            return FALSE;
        }

        if (strlen($errorMessage) > 255) {
            $errorMessage = mb_substr($errorMessage, 0, 254).'…';
        }

        $this->set('error_message', $errorMessage);

        return TRUE;
    }
}
?>