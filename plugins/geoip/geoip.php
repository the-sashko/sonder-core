<?php
/**
 * Plugin For Getting User IP Address And Getting IP Address Metadata
 */
class GeoIPPlugin
{
    const DEFAULT_IP = '0.0.0.0';

    /**
     * Get User IP Address From Request
     *
     * @return string IP Address
     */
    public function getIP(): string
    {
        if (
            array_key_exists('HTTP_CF_CONNECTING_IP', $_SERVER) &&
            !empty($_SERVER['HTTP_CF_CONNECTING_IP'])
        ) {
            return $this->_escapeInput($_SERVER['HTTP_CF_CONNECTING_IP']);
        }

        if (
            array_key_exists('HTTP_CLIENT_IP', $_SERVER) &&
            !empty($_SERVER['HTTP_CLIENT_IP'])
        ) {
            return $this->_escapeInput($_SERVER['HTTP_CLIENT_IP']);
        }

        if (
            array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) &&
            !empty($_SERVER['HTTP_X_FORWARDED_FOR'])
        ) {
            return $this->_escapeInput($_SERVER['HTTP_X_FORWARDED_FOR']);
        }

        if (
            array_key_exists('HTTP_X_FORWARDED', $_SERVER) &&
            !empty($_SERVER['HTTP_X_FORWARDED'])
        ) {
            return $this->_escapeInput($_SERVER['HTTP_X_FORWARDED']);
        }

        if (
            array_key_exists('HTTP_FORWARDED_FOR', $_SERVER) &&
            !empty($_SERVER['HTTP_FORWARDED_FOR'])
        ) {
            return $this->_escapeInput($_SERVER['HTTP_FORWARDED_FOR']);
        }

        if (
            array_key_exists('HTTP_FORWARDED', $_SERVER) &&
            !empty($_SERVER['HTTP_FORWARDED'])
        ) {
            return $this->_escapeInput($_SERVER['HTTP_FORWARDED']);
        }

        if (
            array_key_exists('REMOTE_ADDR', $_SERVER) &&
            !empty($_SERVER['REMOTE_ADDR'])
        ) {
            return $this->_escapeInput($_SERVER['REMOTE_ADDR']);
        }

        return static::DEFAULT_IP;
    }

    /**
     * Get Hash From User IP Address From Request
     *
     * @return string IP Address
     */
    public function getIPHash(): string
    {
        return hash('sha256', $this->getIP());
    }

    public function _escapeInput(?string $inputString = null): string
    {
        $inputString = (string) $inputString;
        $inputString = strip_tags($inputString);
        $inputString = htmlspecialchars($inputString);
        $inputString = addslashes($inputString);
        $inputString = preg_replace('/\s/su', '', $inputString);

        if (empty($inputString)) {
            $inputString = static::DEFAULT_IP;
        }

        return $inputString;
    }
}
