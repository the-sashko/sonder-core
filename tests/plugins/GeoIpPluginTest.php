<?php
use PHPUnit\Framework\TestCase;

/**
 * Class For Testing GeoIpPlugin Class Methods
 */
class GeoIpPluginTest extends TestCase
{
    const DEFAULT_IP_SAMPLE = '0.0.0.0';

    const CLOUD_FLARE_IP_SAMPLE = '123.123.123.123';

    const CLIENT_IP_SAMPLE = '124.124.124.124';

    const X_FORWARDED_FOR_IP_SAMPLE  = '125.125.125.125';

    const X_FORWARDED_IP_SAMPLE  = '126.126.126.126';

    const FORWARDED_FOR_IP_SAMPLE  = '127.127.127.127';

    const FORWARDED_IP_SAMPLE  = '128.128.128.128';

    const REMOTE_ADDR_IP_SAMPLE = '129.129.129.129';

    public function testGetIp()
    {
        $plugin = $this->_getPlugin();

        $this->_prepareServerVariables();

        $this->assertEquals(static::CLOUD_FLARE_IP_SAMPLE, $plugin->getIp());

        unset($_SERVER['HTTP_CF_CONNECTING_IP']);

        $this->assertEquals(static::CLIENT_IP_SAMPLE, $plugin->getIp());

        unset($_SERVER['HTTP_CLIENT_IP']);

        $this->assertEquals(
            static::X_FORWARDED_FOR_IP_SAMPLE,
            $plugin->getIp()
        );

        unset($_SERVER['HTTP_X_FORWARDED_FOR']);

        $this->assertEquals(static::X_FORWARDED_IP_SAMPLE, $plugin->getIp());

        unset($_SERVER['HTTP_X_FORWARDED']);

        $this->assertEquals(static::FORWARDED_FOR_IP_SAMPLE, $plugin->getIp());

        unset($_SERVER['HTTP_FORWARDED_FOR']);

        $this->assertEquals(static::FORWARDED_IP_SAMPLE, $plugin->getIp());

        unset($_SERVER['HTTP_FORWARDED']);

        $this->assertEquals(static::REMOTE_ADDR_IP_SAMPLE, $plugin->getIp());

        unset($_SERVER['REMOTE_ADDR']);

        $this->assertEquals(static::DEFAULT_IP_SAMPLE, $plugin->getIp());
    }

    public function testGetIpHash()
    {
        $plugin = $this->_getPlugin();

        $this->_prepareServerVariables();

        $this->assertEquals(
            hash('sha256', static::CLOUD_FLARE_IP_SAMPLE),
            $plugin->getIpHash()
        );

        unset($_SERVER['HTTP_CF_CONNECTING_IP']);

        $this->assertEquals(
            hash('sha256', static::CLIENT_IP_SAMPLE),
            $plugin->getIpHash()
        );

        unset($_SERVER['HTTP_CLIENT_IP']);

        $this->assertEquals(
            hash('sha256', static::X_FORWARDED_FOR_IP_SAMPLE),
            $plugin->getIpHash()
        );

        unset($_SERVER['HTTP_X_FORWARDED_FOR']);

        $this->assertEquals(
            hash('sha256', static::X_FORWARDED_IP_SAMPLE),
            $plugin->getIpHash()
        );

        unset($_SERVER['HTTP_X_FORWARDED']);

        $this->assertEquals(
            hash('sha256', static::FORWARDED_FOR_IP_SAMPLE),
            $plugin->getIpHash()
        );

        unset($_SERVER['HTTP_FORWARDED_FOR']);

        $this->assertEquals(
            hash('sha256', static::FORWARDED_IP_SAMPLE),
            $plugin->getIpHash()
        );

        unset($_SERVER['HTTP_FORWARDED']);

        $this->assertEquals(
            hash('sha256', static::REMOTE_ADDR_IP_SAMPLE),
            $plugin->getIpHash()
        );

        unset($_SERVER['REMOTE_ADDR']);

        $this->assertEquals(
            hash('sha256', static::DEFAULT_IP_SAMPLE),
            $plugin->getIpHash()
        );
    }

    private function _getPlugin(): GeoIpPlugin
    {
        if (empty($this->_plugin)) {
            $this->_plugin = (new CommonCore)->getPlugin('geoip');
        }

        return $this->_plugin;
    }

    private function _prepareServerVariables(): void
    {
        $_SERVER['HTTP_CF_CONNECTING_IP'] = static::CLOUD_FLARE_IP_SAMPLE;
        $_SERVER['HTTP_CLIENT_IP']        = static::CLIENT_IP_SAMPLE;
        $_SERVER['HTTP_X_FORWARDED_FOR']  = static::X_FORWARDED_FOR_IP_SAMPLE;
        $_SERVER['HTTP_X_FORWARDED']      = static::X_FORWARDED_IP_SAMPLE;
        $_SERVER['HTTP_FORWARDED_FOR']    = static::FORWARDED_FOR_IP_SAMPLE;
        $_SERVER['HTTP_FORWARDED']        = static::FORWARDED_IP_SAMPLE;
        $_SERVER['REMOTE_ADDR']           = static::REMOTE_ADDR_IP_SAMPLE;
    }
}
