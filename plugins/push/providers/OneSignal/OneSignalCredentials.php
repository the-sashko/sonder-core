<?php
class OneSignalCredentials implements IPushCredentials
{
    private $_data = [];

    public function __construct()
    {
        $configFile = __DIR__.'/../../../../../config/push.json';

        if (!file_exists($configFile) || !is_file($configFile)) {
            throw new Exception('Missing Push Config');
        }

        $credentialsData = file_get_contents($configFile);
        $credentialsData = (array) json_decode($credentialsData, true);

        $this->_data = $credentialsData;
    }

    public function getURL(): string
    {
        if (!array_key_exists('url', $this->_data)) {
            return '';
        }

        return (string) $this->_data['url'];
    }
    
    public function getLogin(): string
    {
        if (!array_key_exists('login', $this->_data)) {
            return '';
        }

        return (string) $this->_data['login'];
    }

    public function getToken(): string
    {
        if (!array_key_exists('token', $this->_data)) {
            return '';
        }
        
        return (string) $this->_data['token'];
    }

    public function getSubscribersGroup(): string
    {
        if (!array_key_exists('subscribers_group', $this->_data)) {
            return '';
        }

        return (string) $this->_data['subscribers_group'];
    }

    public function getOptions(): array
    {
        if (!array_key_exists('options', $this->_data)) {
            return [];
        }
        
        return (array) $this->_data['options'];
    }

    public function getDefaultMessageURL(): string
    {
        if (!array_key_exists('default_message_url', $this->_data)) {
            return [];
        }
        
        return (string) $this->_data['default_message_url'];
    }

    public function getDefaultMessageTitle(): ?string
    {
        if (!array_key_exists('default_message_title', $this->_data)) {
            return null;
        }
        
        return (string) $this->_data['default_message_title'];
    }

    public function getDefaultMessageImage(): string
    {
        if (!array_key_exists('default_message_image', $this->_data)) {
            return [];
        }
        
        return (string) $this->_data['default_message_image'];
    }

    public function setURL(string $url = '#'): void
    {
        $this->_data['url'] = $url;
    }

    public function setLogin(string $login = ''): void
    {
        $this->_data['login'] = $login;
    }

    public function setToken(string $token = ''): void
    {
        $this->_data['token'] = $token;
    }

    public function setSubscribersGroup(string $subscribersGroup = ''): void
    {
        $this->_data['subscribers_group'] = $subscribersGroup;
    }

    public function setOptions(array $options = []): void
    {
        $this->_data['options'] = $options;
    }

    public function setDefaultMessageURL(string $url = '#'): void
    {
        $this->_data['default_message_url'] = $url;
    }

    public function setDefaultMessageTitle(string $title = ''): void
    {
        $this->_data['default_message_title'] = $title;
    }

    public function setDefaultMessageImage(string $image = ''): void
    {
        $this->_data['default_message_image'] = $image;
    }
}
