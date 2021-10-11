<?php

namespace SonderCore\Core;

class RequestObject
{
    const DEFAULT_METHOD = 'get';

    const DEFAULT_URL = '/';

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
     * @var integer|null
     */
    private ?int $_time = null;


    /**
     * @var RouterObject|null
     */
    private ?RouterObject $_route = null;

    public function __construct()
    {
        $this->_setHttpMethod();

        $this->setUrlValues($_GET);
        $this->setPostValues($_POST);

        $this->_setHost();
        $this->setUrl();

        $this->_setTime();

        $this->_setRoute();

        $this->_removeGlobalInputValues();
    }

    /**
     * @return string
     */
    final public function getHttpMethod(): string
    {
        if (empty($this->_httpMethod)) {
            return static::DEFAULT_METHOD;
        }

        return $this->_httpMethod;
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
     *
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
     * @return string
     *
     * @throws \Exception
     */
    final public function getHost(): string
    {
        if (empty($this->_host)) {
            throw new \Exception('Host Is Not Set!');
        }

        return $this->_host;
    }

    /**
     * @return string
     */
    final public function getUrl(): string
    {
        if (empty($this->_url)) {
            return static::DEFAULT_URL;
        }

        return $this->_url;
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
     * @return int|null
     */
    final public function getTime(): ?int
    {
        return $this->_time;
    }

    /**
     * @return RouterObject|null
     */
    final public function getRoute(): ?RouterObject
    {
        return $this->_route;
    }

    /**
     * @return string
     *
     * @throws Exception
     */
    final public function getFullUrl(): string
    {
        $host = $this->getHost();
        $url = $this->getUrl();

        return sprintf('%s%s', $host, $url);
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
     * @param string|null $url
     */
    final public function setUrl(?string $url = null): void
    {
        if (empty($url)) {
            $url = $_SERVER['REQUEST_URI'];
        }

        $this->_url = $url;
    }

    /**
     * @param string|null $ip
     */
    final public function setIp(?string $ip = null): void
    {
        $this->_ip = $ip;
    }

    /**
     * @param string|null $userAgent
     */
    final public function setUserAgent(?string $userAgent = null): void
    {
        $this->_userAgent = $userAgent;
    }

    private function _setHttpMethod(): void
    {
        $this->_httpMethod = static::DEFAULT_METHOD;

        $method = $_SERVER['REQUEST_METHOD'];
        $method = mb_convert_case($method, MB_CASE_LOWER);

        if (!empty($method)) {
            $this->_httpMethod = $method;
        }
    }

    private function _setHost(): void
    {
        $protocol = 'http';

        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
            $protocol = 'https';
        }

        $this->_host = sprintf('%s://%s', $protocol, $_SERVER['HTTP_HOST']);
    }

    private function _setTime(): void
    {
        $this->_time = time();
    }

    private function _setRoute(): void
    {
        $this->_route = new RouterObject();
    }

    private function _removeGlobalInputValues(): void
    {
        $_GET = [];
        $_POST = [];
        $_REQUEST = [];
    }
}
