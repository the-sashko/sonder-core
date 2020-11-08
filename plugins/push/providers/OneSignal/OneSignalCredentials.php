<?php
class OneSignalCredentials implements IPushCredentials
{
    private $_data = [];

    public function __construct()
    {
        $configFile = __DIR__.'/../../../../../config/push.json';

        if (!file_exists($configFile) || !is_file($configFile)) {
            throw new \Exception('Missing Push Config');
        }

        $credentialsData = file_get_contents($configFile);
        $credentialsData = (array) json_decode($credentialsData, true);

        $this->_data = $credentialsData;
    }

    public function getUrl(): ?string
    {
        if (!array_key_exists('url', $this->_data)) {
            return null;
        }

        return $this->_data['url'];
    }
    
    public function getLogin(): ?string
    {
        if (!array_key_exists('login', $this->_data)) {
            return null;
        }

        return $this->_data['login'];
    }

    public function getToken(): ?string
    {
        if (!array_key_exists('token', $this->_data)) {
            return null;
        }
        
        return $this->_data['token'];
    }

    public function getSubscribersGroup(): ?string
    {
        if (!array_key_exists('subscribers_group', $this->_data)) {
            return null;
        }

        return $this->_data['subscribers_group'];
    }

    public function getOptions(): ?array
    {
        if (!array_key_exists('options', $this->_data)) {
            return null;
        }
        
        return $this->_data['options'];
    }

    public function getDefaultMessageUrl(): ?string
    {
        if (!array_key_exists('default_message_url', $this->_data)) {
            return null;
        }
        
        return $this->_data['default_message_url'];
    }

    public function getDefaultMessageTitle(): ?string
    {
        if (!array_key_exists('default_message_title', $this->_data)) {
            return null;
        }
        
        return $this->_data['default_message_title'];
    }

    public function getDefaultMessageImage(): ?string
    {
        if (!array_key_exists('default_message_image', $this->_data)) {
            return null;
        }
        
        return $this->_data['default_message_image'];
    }

    public function setUrl(?string $url = null): void
    {
        $this->_data['url'] = $url;
    }

    public function setLogin(?string $login = null): void
    {
        $this->_data['login'] = $login;
    }

    public function setToken(?string $token = null): void
    {
        $this->_data['token'] = $token;
    }

    public function setSubscribersGroup(?string $subscribersGroup = null): void
    {
        $this->_data['subscribers_group'] = $subscribersGroup;
    }

    public function setOptions(?array $options = null): void
    {
        $this->_data['options'] = $options;
    }

    public function setDefaultMessageUrl(?string $url = null): void
    {
        $this->_data['default_message_url'] = $url;
    }

    public function setDefaultMessageTitle(?string $title = null): void
    {
        $this->_data['default_message_title'] = $title;
    }

    public function setDefaultMessageImage(?string $image = null): void
    {
        $this->_data['default_message_image'] = $image;
    }
}
