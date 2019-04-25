<?php
/**
 * Trait For Processing Forms In Cron Model
 */
trait CronForm
{
    /**
     * Processing Form For Cron Jobs
     *
     * @param array $formData Data From Form
     * @param int   $cronID   Cron Job ID
     *
     * @return array Result Of Form Processing
     */
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

    /**
     * Get Prepared Form Data
     *
     * @param array $formData Data From Form
     * @param int   $cronID   Cron ID
     *
     * @return array Prepared Form Data
     */
    protected function _getFormFields(
        array $formData = [],
        int   $cronID    = -1
    ) : array
    {
        $cronVO = $this->getVO($formData);

        $action = $cronVO->getAction();
        $interval = $cronVO->getInterval();
        $isActive = $cronVO->getIsActive();

        $action = preg_replace('/\s+/su', '', $action);

        return [$action, $interval, $isActive];
    }

    /**
     * Validate Data From Form
     *
     * @param string $action   Cron Job Action
     * @param int    $interval Cron Job Execution Interval
     * @param bool   $isActive Is Cron Job Active
     *
     * @return array Result Of Form Data Validation
     */
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
