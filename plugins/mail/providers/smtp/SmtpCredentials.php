<?php

namespace Sonder\Plugins\Mail\Providers\Smtp;

use Exception;
use Sonder\Plugins\Mail\Interfaces\IMailCredentials;

final class SmtpCredentials implements IMailCredentials
{
    private const CONFIG_FILE_PATH_PATTERN = __DIR__ . '/../../../../../config/%s.json';

    private const DEFAULT_REPLY_EMAIL = 'noreply@noreply.noreply';

    private const DEFAULT_SENDER_NAME = 'Web Service Automatic Mailer';

    /**
     * @var string|null
     */
    private ?string $_serverAddress = null;

    /**
     * @var int|null
     */
    private ?int $_serverPort = null;

    /**
     * @var string|null
     */
    private ?string $_login = null;

    /**
     * @var string|null
     */
    private ?string $_password = null;

    /**
     * @var string|null
     */
    private ?string $_replyEmail = null;

    /**
     * @var string|null
     */
    private ?string $_senderName = null;

    /**
     * @throws Exception
     */
    final public function __construct()
    {
        if (!$this->_loadConfig()) {
            throw new Exception('Can Not Load SMTP Config');
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getServerAddress(): string
    {
        if (empty($this->_serverAddress)) {
            throw new Exception('SMTP Server Address Is Not Set');
        }

        return (string)$this->_serverAddress;
    }

    /**
     * @return int
     * @throws Exception
     */
    public function getServerPort(): int
    {
        if (empty($this->_serverPort)) {
            throw new Exception('SMTP Server Port Is Not Set');
        }

        return (int)$this->_serverPort;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getLogin(): string
    {
        if (empty($this->_login)) {
            throw new Exception('SMTP Login Is Not Set');
        }

        return (string)$this->_login;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getPassword(): string
    {
        if (empty($this->_password)) {
            throw new Exception('SMTP Password Is Not Set');
        }

        return (string)$this->_password;
    }

    /**
     * @return string
     */
    public function getReplyEmail(): string
    {
        if (empty($this->_replyEmail)) {
            return SmtpCredentials::DEFAULT_REPLY_EMAIL;
        }

        return (string)$this->_replyEmail;
    }

    /**
     * @return string
     */
    public function getSenderName(): string
    {
        if (empty($this->_senderName)) {
            return SmtpCredentials::DEFAULT_SENDER_NAME;
        }

        return (string)$this->_senderName;
    }

    /**
     * @param string|null $serverAddress
     */
    private function _setServerAddress(?string $serverAddress = null): void
    {
        if (!empty($serverAddress)) {
            $this->_serverAddress = $serverAddress;
        }
    }

    /**
     * @param int|null $serverPort
     */
    private function _setServerPort(?int $serverPort = null): void
    {
        $serverPort = (int)$serverPort;

        if ($serverPort > 0) {
            $this->_serverPort = $serverPort;
        }
    }

    /**
     * @param string|null $login
     */
    private function _setLogin(?string $login = null): void
    {
        if (!empty($login)) {
            $this->_login = $login;
        }
    }

    /**
     * @param string|null $password
     */
    private function _setPassword(?string $password = null): void
    {
        if (!empty($password)) {
            $this->_password = $password;
        }
    }

    /**
     * @param string|null $replyEmail
     */
    private function _setReplyEmail(?string $replyEmail = null): void
    {
        if (!empty($replyEmail)) {
            $replyEmail = SmtpCredentials::DEFAULT_REPLY_EMAIL;
        }

        $this->_replyEmail = $replyEmail;
    }

    /**
     * @param string|null $senderName
     */
    private function _setSenderName(?string $senderName = null): void
    {
        if (!empty($senderName)) {
            $senderName = SmtpCredentials::DEFAULT_SENDER_NAME;
        }

        $this->_senderName = $senderName;
    }

    /**
     * @param string|null $configIdent
     * @return array|null
     */
    private function _getConfigData(?string $configIdent = null): ?array
    {
        if (empty($configIdent)) {
            return null;
        }

        $configFilePath = SmtpCredentials::CONFIG_FILE_PATH_PATTERN;

        if (defined('APP_PROTECTED_DIR_PATH')) {
            $configFilePath = sprintf(
                '%s/config/%s.json',
                APP_PROTECTED_DIR_PATH,
                '%s'
            );
        }

        $configFilePath = sprintf($configFilePath, $configIdent);

        if (!file_exists($configFilePath) || !is_file($configFilePath)) {
            return null;
        }

        $configData = file_get_contents($configFilePath);

        return (array)json_decode($configData, true);
    }

    /**
     * @return bool
     */
    private function _loadConfig(): bool
    {
        $mailConfigData = (array)$this->_getConfigData('mail');
        $mainConfigData = (array)$this->_getConfigData('main');

        if (!array_key_exists('smtp', $mailConfigData)) {
            return false;
        }

        $smtpConfig = (array)$mailConfigData['smtp'];

        if (array_key_exists('server', $smtpConfig)) {
            $this->_setServerAddress($smtpConfig['server']);
        }

        if (array_key_exists('port', $smtpConfig)) {
            $this->_setServerPort((int)$smtpConfig['port']);
        }

        if (array_key_exists('login', $smtpConfig)) {
            $this->_setLogin($smtpConfig['login']);
        }

        if (array_key_exists('password', $smtpConfig)) {
            $this->_setPassword($smtpConfig['password']);
        }

        if (array_key_exists('admin_email', $mainConfigData)) {
            $this->_setReplyEmail($mainConfigData['admin_email']);
        }

        if (array_key_exists('site_name', $mainConfigData)) {
            $this->_setSenderName($mainConfigData['site_name']);
        }

        return true;
    }
}
