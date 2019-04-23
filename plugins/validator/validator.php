<?php
/**
 * Plugin For Validation Values Formats
 */
class ValidatorPlugin
{
    /**
     * Validation Value By Validation Type
     *
     * @param string $value Value For Validation
     * @param string $type  Type For Validation Format
     *
     * @return bool Validation Result
     */
    public function isValid(string $value = null, string $type = '') : bool
    {
        $validatorAction = '_isValid'.mb_convert_case($type, MB_CASE_TITLE);

        return $this->$validatorAction($value);
    }

    /**
     * Validation Value By Money Format
     *
     * @param string $value Value For Validation
     *
     * @return bool Validation Result
     */
    private function _isValidMoney(string $value = '') : bool
    {
        return preg_match('/^([0-9]+)(\,|\.)([0-9]{2})$/su', $value);
    }

    /**
     * Validation Value By Title Format
     *
     * @param string $value Value For Validation
     *
     * @return bool Validation Result
     */
    private function _isValidTitle(string $value = '') : bool
    {
        return strlen($value) <= 255 && strlen(trim($value)) >= 3;
    }

    /**
     * Validation Value By ID Format
     *
     * @param string $value Value For Validation
     *
     * @return bool Validation Result
     */
    private function _isValidID(string $value = '') : bool
    {
        if (!preg_match('/^([0-9]+)$/su', $value)) {
            return false;
        }

        $value = (int) $value;

        return $value > 0;
    }

    /**
     * Validation Value By Email Format
     *
     * @param string $value Value For Validation
     *
     * @return bool Validation Result
     */
    private function _isValidEmail(string $value = '') : bool
    {
        return preg_match('/^(.*?)\@(.*?)\.(.*?)$/su', $value);
    }

    /**
     * Validation Value By Domain Format
     *
     * @param string $value Value For Validation
     *
     * @return bool Validation Result
     */
    private function _isValidDomain(string $value = '') : bool
    {
        return preg_match('/^([0-9a-z\.\-]+)\.([a-z]+)$/su', $value);
    }

    /**
     * Validation Value By URL Format
     *
     * @param string $value Value For Validation
     *
     * @return bool Validation Result
     */
    private function _isValidURL(string $value = '') : bool
    {
        return preg_match(
            '/^((http)|(https)):\/\/(([0-9a-z\.\-]+)\.([a-z]+))(.*?)$/su',
            $value
        );
    }

    /**
     * Validation Value By Age Format
     *
     * @param string $value Value For Validation
     *
     * @return bool Validation Result
     */
    private function _isValidAge(string $value = '') : bool
    {
        if (!preg_match('/^([0-9]+)$/su', $value)) {
            return false;
        }

        $value = (int) $value;

        return $value > 0 && $value < 100;
    }

    /**
     * Validation Value By Slug Format
     *
     * @param string $value Value For Validation
     *
     * @return bool Validation Result
     */
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