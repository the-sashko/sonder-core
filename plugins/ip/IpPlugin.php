<?php

namespace Sonder\Plugins;

final class IpPlugin
{
    const DEFAULT_IP = '0.0.0.0';

    final public function getIp(): string
    {
        $ipAddress = IpPlugin::DEFAULT_IP;

        $this->_setIpFromDefaultHttpHeaders($ipAddress);
        $this->_setIpFromForwardedHttpHeaders($ipAddress);
        $this->_setIpFromClientHttpHeaders($ipAddress);
        $this->_setIpFromCloudflareHttpHeaders($ipAddress);

        return $ipAddress;
    }

    final public function getIpHash(): string
    {
        return hash('sha256', $this->getIp());
    }

    public function _escapeInput(?string $inputString = null): string
    {
        $inputString = (string)$inputString;
        $inputString = strip_tags($inputString);
        $inputString = htmlspecialchars($inputString);
        $inputString = addslashes($inputString);
        $inputString = preg_replace('/\s/su', '', $inputString);

        if (empty($inputString)) {
            $inputString = IpPlugin::DEFAULT_IP;
        }

        return $inputString;
    }

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

    private function _setIpFromDefaultHttpHeaders(string &$ipAddress): void
    {
        if (
            array_key_exists('REMOTE_ADDR', $_SERVER) &&
            !empty($_SERVER['REMOTE_ADDR'])
        ) {
            $ipAddress = $this->_escapeInput($_SERVER['REMOTE_ADDR']);
        }
    }

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
