<?php

namespace Sonder\Core;

use Exception;
use Sonder\Core\Interfaces\IUser;

final class RequestObject
{
    const DEFAULT_HTTP_METHOD = 'get';

    const DEFAULT_URL = '/';

    const DEFAULT_LANGUAGE = 'en';

    /**
     * @var string|null
     */
    private ?string $_httpMethod = null;

    /**
     * @var array|null
     */
    private ?array $_urlValues = null;

    /**
     * @var array|null
     */
    private ?array $_postValues = null;

    /**
     * @var array|null
     */
    private ?array $_apiValues = null;

    /**
     * @var array|null
     */
    private ?array $_cliValues = null;

    /**
     * @var string|null
     */
    private ?string $_host = null;

    /**
     * @var string|null
     */
    private ?string $_url = null;

    /**
     * @var string|null
     */
    private ?string $_ip = null;

    /**
     * @var string|null
     */
    private ?string $_userAgent = null;

    /**
     * @var object|null
     */
    private ?object $_session = null;

    /**
     * @var IUser|null
     */
    private ?IUser $_user = null;

    /**
     * @var string|null
     */
    private ?string $_controller = null;

    /**
     * @var string|null
     */
    private ?string $_method = null;

    /**
     * @var bool
     */
    private bool $_noCache = false;

    /**
     * @var integer|null
     */
    private ?int $_time = null;

    /**
     * @var string
     */
    private string $_language;

    final public function __construct()
    {
        $this->_setHttpMethod();

        $this->_setHost();
        $this->_setUrl();

        $this->setUrlValues($_GET);
        $this->setPostValues($_POST);

        $this->setLanguage();

        $this->_setUserAgent();

        $this->_setTime();

        $this->_removeGlobalInputValues();
    }

    /**
     * @return string
     */
    final public function getHttpMethod(): string
    {
        if (empty($this->_httpMethod)) {
            return RequestObject::DEFAULT_HTTP_METHOD;
        }

        return $this->_httpMethod;
    }

    /**
     * @return string
     * @throws Exception
     */
    final public function getHost(): string
    {
        if (empty($this->_host)) {
            throw new Exception('Host Is Not Set!');
        }

        return $this->_host;
    }

    /**
     * @return string
     */
    final public function getUrl(): string
    {
        if (empty($this->_url)) {
            return RequestObject::DEFAULT_URL;
        }

        return $this->_url;
    }

    /**
     * @return string
     * @throws Exception
     */
    final public function getFullUrl(): string
    {
        $host = $this->getHost();
        $url = $this->getUrl();

        return sprintf('%s%s', $host, $url);
    }

    /**
     * @return array|null
     */
    final public function getUrlValues(): ?array
    {
        return $this->_urlValues;
    }

    /**
     * @param string|null $valueName
     * @return string|null
     */
    final public function getUrlValue(?string $valueName = null): ?string
    {
        if (empty($valueName)) {
            return null;
        }

        if (empty($this->_urlValues)) {
            return null;
        }

        if (!array_key_exists($valueName, $this->_urlValues)) {
            return null;
        }

        if (empty($this->_urlValues[$valueName])) {
            return null;
        }

        return (string)$this->_urlValues[$valueName];
    }

    /**
     * @return array|null
     */
    final public function getPostValues(): ?array
    {
        return $this->_postValues;
    }

    /**
     * @return array|null
     */
    final public function getApiValues(): ?array
    {
        return $this->_apiValues;
    }

    /**
     * @return string
     */
    final public function getLanguage(): string
    {
        return $this->_language;
    }

    /**
     * @return array|null
     */
    final public function getCliValues(): ?array
    {
        return $this->_cliValues;
    }

    /**
     * @return string|null
     */
    final public function getIp(): ?string
    {
        return $this->_ip;
    }

    /**
     * @return string|null
     */
    final public function getUserAgent(): ?string
    {
        return $this->_userAgent;
    }

    /**
     * @return object|null
     */
    final public function getSession(): ?object
    {
        return $this->_session;
    }

    /**
     * @return IUser|null
     */
    final public function getUser(): ?IUser
    {
        return $this->_user;
    }

    /**
     * @return string|null
     */
    final public function getController(): ?string
    {
        return $this->_controller;
    }

    /**
     * @return string|null
     */
    final public function getMethod(): ?string
    {
        return $this->_method;
    }

    /**
     * @return bool
     */
    final public function getNoCache(): bool
    {
        return $this->_noCache;
    }

    /**
     * @return int|null
     */
    final public function getTime(): ?int
    {
        return $this->_time;
    }

    /**
     * @param string|null $ip
     */
    final public function setIp(?string $ip = null): void
    {
        if (!empty($ip)) {
            $this->_ip = $ip;
        }
    }

    /**
     * @param array|null $urlValues
     */
    final public function setUrlValues(?array $urlValues = null): void
    {
        $this->_urlValues = $urlValues;
    }

    /**
     * @param array|null $postValues
     */
    final public function setPostValues(?array $postValues = null): void
    {
        $this->_postValues = $postValues;
    }

    /**
     * @param string|null $language
     */
    final public function setLanguage(?string $language = null): void
    {
        $language = empty($language) ? $this->_getDefaultLanguage() : $language;

        $this->_language = $language;
    }

    /**
     * @param array|null $apiValues
     */
    final public function setApiValues(?array $apiValues = null): void
    {
        $this->_apiValues = $apiValues;
    }

    /**
     * @param array|null $cliValues
     */
    final public function setCliValues(?array $cliValues = null): void
    {
        $this->_cliValues = $cliValues;
    }

    /**
     * @throws Exception
     */
    final public function setSession(): void
    {
        $this->_session = CoreObject::getPlugin('session');
    }

    /**
     * @param IUser $user
     */
    final public function setUser(IUser $user): void
    {
        $this->_user = $user;
    }

    /**
     * @param string|null $controller
     */
    final public function setController(?string $controller = null): void
    {
        $this->_controller = $controller;
    }

    /**
     * @param string|null $method
     */
    final public function setMethod(?string $method = null): void
    {
        $this->_method = $method;
    }

    /**
     * @param bool $noCache
     */
    final public function setNoCache(bool $noCache = false): void
    {
        $this->_noCache = $noCache;
    }

    /**
     * @return string
     */
    private function _getDefaultLanguage(): string
    {
        if (defined('APP_DEFAULT_LANGUAGE')) {
            return APP_DEFAULT_LANGUAGE;
        }

        return RequestObject::DEFAULT_LANGUAGE;
    }

    private function _setHttpMethod(): void
    {
        $this->_httpMethod = RequestObject::DEFAULT_HTTP_METHOD;

        $method = $_SERVER['REQUEST_METHOD'];
        $method = mb_convert_case($method, MB_CASE_LOWER);

        if (!empty($method)) {
            $this->_httpMethod = $method;
        }
    }

    private function _setUrl(): void
    {
        $this->_url = $_SERVER['REQUEST_URI'];
    }

    private function _setHost(): void
    {
        $protocol = 'http';

        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
            $protocol = 'https';
        }

        $this->_host = sprintf('%s://%s', $protocol, $_SERVER['HTTP_HOST']);
    }

    private function _setUserAgent(): void
    {
        $this->_userAgent = $_SERVER['HTTP_USER_AGENT'];
    }

    private function _setTime(): void
    {
        $this->_time = time();
    }

    private function _removeGlobalInputValues(): void
    {
        $_GET = [];
        $_POST = [];
        $_REQUEST = [];
    }
}
