<?php
trait CronForm
{
    public function formHandler(
        ?array $formData = null,
        ?int   $cronId   = null
    ): array
    {
        list(
            $action,
            $interval,
            $isActive
        ) = $this->_getFormFields($formData);

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

        if (empty($cronId)) {
            //$res = $this->_create($action, $interval, $isActive);
        }

        if (!empty($cronId)) {
            //$res = $this->_updateById($action, $interval, $isActive, $cronId);
        }

        if (!$res) {
            $error = 'Internal Database Error';
            return [
                false,
                $error
            ];
        }

        return [
            true,
            null
        ];
    }

    protected function _getFormFields(?array $formData = null): array
    {
        /*$cronVO = $this->getVO($formData);

        $action   = $cronVO->getAction();
        $interval = $cronVO->getInterval();
        $isActive = $cronVO->getIsActive();

        $action = preg_replace('/\s+/su', '', $action);

        return [
            $action,
            $interval,
            $isActive
        ];*/ return [];
    }

    protected function _validateFormFields(
        ?string $action   = null,
        ?int    $interval = null,
        bool    $isActive = false
    ): array
    {
        if (!preg_match('/^job([A-Z])([a-z]+)$/su', (string) $action)) {
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

        /*if ($this->_isCronExists($action, $interval)) {
            $error = 'This Job Is Already Set';
            return [
                false,
                $error
            ];
        }*/

        return [
            true,
            null
        ];
    }
}
