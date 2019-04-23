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
        $serverInfo = new ServerInfo();

        if ($serverInfo->has('HTTP_CF_CONNECTING_IP')) {
            return $serverInfo->get('HTTP_CF_CONNECTING_IP');
        }

        if ($serverInfo->has('HTTP_CLIENT_IP')) {
            return $serverInfo->get('HTTP_CLIENT_IP');
        }

        if ($serverInfo->has('HTTP_X_FORWARDED_FOR')) {
            return $serverInfo->get('HTTP_X_FORWARDED_FOR');
        }

        if ($serverInfo->has('HTTP_X_FORWARDED')) {
            return $serverInfo->get('HTTP_X_FORWARDED');
        }

        if ($serverInfo->has('HTTP_FORWARDED_FOR')) {
            return $serverInfo->get('HTTP_FORWARDED_FOR');
        }

        if ($serverInfo->has('HTTP_FORWARDED')) {
            return $serverInfo->get('HTTP_FORWARDED');
        }

        if ($serverInfo->has('REMOTE_ADDR')) {
            return $serverInfo->get('REMOTE_ADDR');
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
