<?php
/**
 * Plugin For Getting User IP Address And Getting IP Address Metadata
 */
class GeoIpPlugin
{
    const DEFAULT_IP = '0.0.0.0';

    /**
     * Get User IP Address From Request
     *
     * @return string IP Address
     */
    public function getIp(): string
    {
        $ipAddress = static::DEFAULT_IP;

        $this->_setIpFromDefaultHttpHeaders($ipAddress);
        $this->_setIpFromForwardedHttpHeaders($ipAddress);
        $this->_setIpFromClientHttpHeaders($ipAddress);
        $this->_setIpFromCloudflareHttpHeaders($ipAddress);

        return $ipAddress;
    }

    /**
     * Get Hash From User IP Address From Request
     *
     * @return string IP Address Hash
     */
    public function getIpHash(): string
    {
        return hash('sha256', $this->getIp());
    }

    /**
     * Escape Input Values
     *
     * @param string|null $inputString Input String
     *
     * @return string String With Escaped Values
     */
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

    /**
     * Set User IP Address From HTTP Cloudflare  Headers
     *
     * @param string $ipAddress IP Address
     */
    private function _setIpFromCloudflareHttpHeaders(string &$ipAddress): void
    {
        if (
            array_key_exists('HTTP_CF_CONNECTING_IP', $_SERVER) &&
            !empty($_SERVER['HTTP_CF_CONNECTING_IP'])
        ) {
            $ipAddress = $this->_escapeInput(
                $_SERVER['HTTP_CF_CONNECTING_IP']
            );
        }
    }

    /**
     * Set User IP Address From HTTP Forwarded Headers
     *
     * @param string $ipAddress IP Address
     */
    private function _setIpFromForwardedHttpHeaders(string &$ipAddress): void
    {
        if (
            array_key_exists('HTTP_FORWARDED', $_SERVER) &&
            !empty($_SERVER['HTTP_FORWARDED'])
        ) {
            $ipAddress = $this->_escapeInput($_SERVER['HTTP_FORWARDED']);
        }

        if (
            array_key_exists('HTTP_FORWARDED_FOR', $_SERVER) &&
            !empty($_SERVER['HTTP_FORWARDED_FOR'])
        ) {
            $ipAddress = $this->_escapeInput($_SERVER['HTTP_FORWARDED_FOR']);
        }

        if (
            array_key_exists('HTTP_X_FORWARDED', $_SERVER) &&
            !empty($_SERVER['HTTP_X_FORWARDED'])
        ) {
            $ipAddress = $this->_escapeInput($_SERVER['HTTP_X_FORWARDED']);
        }

        if (
            array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) &&
            !empty($_SERVER['HTTP_X_FORWARDED_FOR'])
        ) {
            $ipAddress = $this->_escapeInput($_SERVER['HTTP_X_FORWARDED_FOR']);
        }
    }

    /**
     * Set User IP Address From HTTP Forwarded Headers
     *
     * @param string $ipAddress IP Address
     */
    private function _setIpFromDefaultHttpHeaders(string &$ipAddress): void
    {
        if (
            array_key_exists('REMOTE_ADDR', $_SERVER) &&
            !empty($_SERVER['REMOTE_ADDR'])
        ) {
            $ipAddress = $this->_escapeInput($_SERVER['REMOTE_ADDR']);
        }
    }

    /**
     * Set User IP Address From HTTP Client Headers
     *
     * @param string $ipAddress IP Address
     */
    private function _setIpFromClientHttpHeaders(string &$ipAddress): void
    {
        if (
            array_key_exists('HTTP_CLIENT_IP', $_SERVER) &&
            !empty($_SERVER['HTTP_CLIENT_IP'])
        ) {
            $ipAddress = $this->_escapeInput($_SERVER['HTTP_CLIENT_IP']);
        }
    }
}
