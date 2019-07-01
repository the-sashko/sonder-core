<?php
trait CronForm
{
    public function formHandler(array $formData = [], int $cronID = -1) : array
    {
        list(
            $action,
            $interval,
            $isActive
        ) = $this->_getFormFields($formData, $cronID);

        list($res, $error) = $this->_validateFormFields(
            $action,
            $interval,
            $isActive
        );

        if (!$res) {
            return [
                false,
                $error
            ];
        }

        if ($cronID > 0) {
            $res = $this->_updateByID($action, $interval, $isActive, $cronID);
        } else {
            $res = $this->_create($action, $interval, $isActive);
        }

        if (!$res) {
            $error = 'Internal Database Error';
            return [
                false,
                $error
            ];
        }

        return [true, ''];
    }

    protected function _getFormFields(
        array $formData = [],
        int   $tagID    = -1
    ) : array
    {
        $cronVO = $this->getVO($formData);

        $action = $cronVO->getAction();
        $interval = $cronVO->getInterval();
        $isActive = $cronVO->getIsActive();

        $action = preg_replace('/\s+/su', '', $action);

        return [$action, $interval, $isActive];
    }

    protected function _validateFormFields(
        string $action   = '',
        int    $interval = -1,
        bool   $isActive = false
    ) : array
    {
        if (!preg_match('/^job([A-Z])([a-z]+)$/su', $action)) {
            $error = 'Cron Job Has Invalid Format';
            return [
                false,
                $error
            ];
        }

        if ($interval < 1) {
            $error = 'Invalid Internal Value';
            return [
                false,
                $error
            ];
        }

        if ($this->_isCronExists($action, $interval)) {
            $error = 'This Job Is Already Set';
            return [
                false,
                $error
            ];
        }

        return [true, ''];
    }
}
?>