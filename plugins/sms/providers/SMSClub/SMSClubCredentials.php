<?php
class SMSClubCredentials implements ISMSCredentials
{
    private $_data = [];

    public function __construct()
    {
        $configFile = __DIR__.'/../../../../../config/sms.json';

        if (!file_exists($configFile) || !is_file($configFile)) {
            throw new Exception('Missing SMS Config');
        }

        $credentialsData = file_get_contents($configFile);
        $credentialsData = (array) json_decode($credentialsData, TRUE);

        $this->_data = $credentialsData;
    }

    public function getLogin() : string
    {
        if (!array_key_exists('login', $this->_data)) {
            return '';
        }

        return (string) $this->_data['login'];
    }

    public function getToken() : string
    {
        if (!array_key_exists('token', $this->_data)) {
            return '';
        }

        return (string) $this->_data['token'];
    }

    public function getURL() : string
    {
        if (!array_key_exists('url', $this->_data)) {
            return '';
        }

        return (string) $this->_data['url'];
    }

    public function getAlphaName() : string
    {
        if (!array_key_exists('alpha_name', $this->_data)) {
            return '';
        }

        return (string) $this->_data['alpha_name'];
    }

    public function getOptions() : array
    {
        if (!array_key_exists('options', $this->_data)) {
            return [];
        }

        return (array) $this->_data['options'];
    }

    public function setLogin(string $login = '') : void
    {
        $this->_data['login'] = $login;
    }

    public function setToken(string $token = '') : void
    {
        $this->_data['token'] = $token;
    }

    public function setURL(string $url = '#') : void
    {
        $this->_data['url'] = $url;
    }

    public function setAlphaName(string $alphaName = '') : void
    {
        $this->_data['alpha_name'] = $alphaName;
    }

    public function setOptions(array $options = []) : void
    {
        $this->_data['options'] = $options;
    }
}
?>
