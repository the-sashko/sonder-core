<?php
class GeoIPPlugin {

    /**
     * summary
     */
    public function getIP() : string
    {
        $ip = '0.0.0.0';

        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $ip = $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $this->escapeInput($ip);
    }

    public function getIPHash() : string
    {
        return hash('sha256', $this->getIP());
    }

    /*public function getGeodata($ip = '0.0.0.0') : string {
        $APIKey = $this->configData['security']['geo_api_key'];
        $sql = "
            SELECT
                `country` AS 'country'
            FROM `geoip`
            WHERE `ip` = '{$ip}';
        ";
        $res = $this->select($sql,'geoip');
        if(count($res)>0&&is_array($res[0])&&isset($res[0]['country'])&&strlen($res[0]['country'])>0){
            return $res[0]['country'];
        } else {
            $url = "http://api.ipstack.com/{$ip}?access_key={$APIKey}&format=1";
            $res = file_get_contents($url);
            var_dump($res);
            die();
        }
    }*/
}
?>