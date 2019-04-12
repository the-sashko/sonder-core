<?php
/**
 * Plugin For Getting User IP Address And Getting IP Address Metadata
 */
class GeoIPPlugin
{
    /**
     * Get User IP Address From Request
     * 
     * @return string IP Address
     */
    public function getIP() : string
    {
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            return $this->escapeInput($_SERVER["HTTP_CF_CONNECTING_IP"]);
        }

        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            return $this->escapeInput($_SERVER['HTTP_CLIENT_IP']);
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $this->escapeInput($_SERVER['HTTP_X_FORWARDED_FOR']);
        }

        if (isset($_SERVER['HTTP_X_FORWARDED'])) {
            return $this->escapeInput($_SERVER['HTTP_X_FORWARDED']);
        }

        if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            return $this->escapeInput($_SERVER['HTTP_FORWARDED_FOR']);
        }

        if (isset($_SERVER['HTTP_FORWARDED'])) {
            return $this->escapeInput($_SERVER['HTTP_FORWARDED']);
        }

        if (isset($_SERVER['REMOTE_ADDR'])) {
            return $this->escapeInput($_SERVER['REMOTE_ADDR']);
        }

        return '0.0.0.0';
    }

    /**
     * Get Hash From User IP Address From Request
     *
     * @return string IP Address
     */
    public function getIPHash() : string
    {
        return hash('sha256', $this->getIP());
    }
}
?>