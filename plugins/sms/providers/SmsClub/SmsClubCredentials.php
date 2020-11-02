<?php
class SmsClubCredentials implements ISmsCredentials
{
    const CONFIG_FILE_PATH = __DIR__.'/../../../../../config/sms.json';

    private $_data = null;

    public function __construct()
    {
        if (
            !file_exists(static::CONFIG_FILE_PATH) ||
            !is_file(static::CONFIG_FILE_PATH)
        ) {
            throw new \Exception('Missing SMS Config');
        }

        $credentialsData = file_get_contents(static::CONFIG_FILE_PATH);
        $credentialsData = (array) json_decode($credentialsData, true);

        $this->_data = $credentialsData;
    }

    public function getLogin(): ?string
    {
        if (!array_key_exists('login', $this->_data)) {
            return null;
        }

        return (string) $this->_data['login'];
    }

    public function getToken(): ?string
    {
        if (!array_key_exists('token', $this->_data)) {
            return null;
        }

        return (string) $this->_data['token'];
    }

    public function getUrl(): ?string
    {
        if (!array_key_exists('url', $this->_data)) {
            return null;
        }

        return (string) $this->_data['url'];
    }

    public function getAlphaName(): ?string
    {
        if (!array_key_exists('alpha_name', $this->_data)) {
            return null;
        }

        return (string) $this->_data['alpha_name'];
    }

    public function getOptions(): ?array
    {
        if (!array_key_exists('options', $this->_data)) {
            return null;
        }

        return (array) $this->_data['options'];
    }

    public function setLogin(?string $login = null): void
    {
        $this->_data['login'] = $login;
    }

    public function setToken(?string $token = null): void
    {
        $this->_data['token'] = $token;
    }

    public function setUrl(?string $url = null): void
    {
        $this->_data['url'] = $url;
    }

    public function setAlphaName(?string $alphaName = null): void
    {
        $this->_data['alpha_name'] = $alphaName;
    }

    public function setOptions(?array $options = null): void
    {
        $this->_data['options'] = $options;
    }
}
