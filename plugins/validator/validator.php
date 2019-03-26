<?php
class ValidatorPlugin
{
    public function isValid(string $value = NULL, string $type = '') : bool
    {
        $validatorAction = '_isValid'.
                           mb_convert_case($type, MB_CASE_TITLE);
        return $this->$validatorAction($value);
    }

    private function _isValidMoney(string $value = '') : bool
    {
        return preg_match('/^([0-9]+)(\,|\.)([0-9]{2})$/su', $value);
    }

    private function _isValidTitle(string $value = '') : bool
    {
        return strlen($value) <= 255 && strlen(trim($value)) >= 3;
    }

    private function _isValidID(string $value = '') : bool
    {
        if (!preg_match('/^([0-9]+)$/su', $value)) {
            return false;
        }

        $value = (int) $value;

        return $value > 0;
    }

    private function _isValidEmail(string $value = '') : bool
    {
        return preg_match('/^(.*?)\@(.*?)\.(.*?)$/su', $value);
    }

    private function _isValidDomain(string $value = '') : bool
    {
        return preg_match('/^([0-9a-z\.\-]+)\.([a-z]+)$/su', $value);
    }

    private function _isValidURL(string $value = '') : bool
    {
        return preg_match(
            '/^((http)|(https)):\/\/(([0-9a-z\.\-]+)\.([a-z]+))(.*?)$/su',
            $value
        );
    }

    private function _isValidAge(string $value = '') : bool
    {
        if (!preg_match('/^([0-9]+)$/su', $value)) {
            return false;
        }

        $value = (int) $value;

        return $value > 0 && $value < 100;
    }

    private function _isValidSlug(string $value = '') : bool
    {
        if (!$this->_isValidTitle($value)) {
            return false;
        }

        if (preg_match('/^(.*?)\-\-(.*?)$/su', $value)) {
            return false;
        }

        if (preg_match('/(^\-(.*?)$)|(^(.*?)\-$)/su', $value)) {
            return false;
        }

        return preg_match('/^([0-9a-z\-]+)$/su', $value);
    }
}
?>