<?php
class SmtpCredentials implements IMailCredentials
{
    const DEFAULT_REPLY_EMAIL = 'noreply@noreply.noreply';

    const DEFAULT_SENDER_NAME = 'Web Service Automatic Mailer';

    private $_serverAddress = null;

    private $_serverPort = null;

    private $_login = null;

    private $_password = null;

    private $_replyEmail = null;

    private $_senderName = null;

    public function __construct()
    {
        $this->_loadConfig();
    }

    private function _loadConfig(): bool
    {
        $mailConfigData = (array) $this->_getConfigData('mail');
        $mainConfigData = (array) $this->_getConfigData('main');

        if (!array_key_exists('smtp', $mailConfigData)) {
            return false;
        }

        $smptpConfig = (array) $mailConfigData['smtp'];

        if (array_key_exists('server', $smptpConfig)) {
            $this->_setServerAddress($smptpConfig['server']);
        }

        if (array_key_exists('port', $smptpConfig)) {
            $this->_setServerPort((int) $smptpConfig['port']);
        }

        if (array_key_exists('login', $smptpConfig)) {
            $this->_setLogin($smptpConfig['login']);
        }

        if (array_key_exists('password', $smptpConfig)) {
            $this->_setPassword($smptpConfig['password']);
        }

        if (array_key_exists('admin_email', $mainConfigData)) {
            $this->_setReplyEmail($mainConfigData['admin_email']);
        }

        if (array_key_exists('site_name', $mainConfigData)) {
            $this->_setSenderName($mainConfigData['site_name']);
        }

        return true;
    }

    public function getServerAddress(): string
    {
        if (empty($this->_serverAddress)) {
            throw new \Exception('SMTP Server Address Is Not Set');
        }

        return (string) $this->_serverAddress;
    }

    public function getServerPort(): int
    {
        if (empty($this->_serverPort)) {
            throw new \Exception('SMTP Server Port Is Not Set');
        }

        return (int) $this->_serverPort;
    }

    public function getLogin(): string
    {
        if (empty($this->_login)) {
            throw new \Exception('SMTP Login Is Not Set');
        }

        return (string) $this->_login;
    }

    public function getPassword(): string
    {
        if (empty($this->_password)) {
            throw new \Exception('SMTP Password Is Not Set');
        }

        return (string) $this->_password;
    }

    public function getReplyEmail(): string
    {
        if (empty($this->_replyEmail)) {
            return static::DEFAULT_REPLY_EMAIL;
        }

        return (string) $this->_replyEmail;
    }

    public function getSenderName(): string
    {
        if (empty($this->_senderName)) {
            return static::DEFAULT_SENDER_NAME;
        }

        return (string) $this->_senderName;
    }

    private function _setServerAddress(?string $serverAddress = null): void
    {
        if (!empty($serverAddress)) {
            $this->_serverAddress = $serverAddress;
        }
    }

    private function _setServerPort(?int $serverPort = null): void
    {
        $serverPort = (int) $serverPort;

        if ($serverPort > 0) {
            $this->_serverPort = $serverPort;
        }
    }

    private function _setLogin(?string $loign = null): void
    {
        if (!empty($loign)) {
            $this->_login = $loign;
        }
    }

    private function _setPassword(?string $password = null): void
    {
        if (!empty($password)) {
            $this->_password = $password;
        }
    }

    private function _setReplyEmail(?string $replyEmail = null): void
    {
        if (!empty($replyEmail)) {
            $replyEmail = static::DEFAULT_REPLY_EMAIL;
        }

        $this->_replyEmail = $replyEmail;
    }

    private function _setSenderName(?string $senderName = null): void
    {
        if (!empty($senderName)) {
            $senderName = static::DEFAULT_SENDER_NAME;
        }

        $this->_senderName = $senderName;
    }

    private function _getConfigData(?string $configIdent = null): ?array
    {
        if (empty($configIdent)) {
            return null;
        }

        $configFilePath = __DIR__.'/../../../../../config/%s.json';
        $configFilePath = sprintf($configFilePath, $configIdent);

        if (!file_exists($configFilePath) || !is_file($configFilePath)) {
            return null;
        }

        $configData = file_get_contents($configFilePath);

        return (array) json_decode($configData, true);
    }
}
