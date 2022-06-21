<?php

namespace Sonder\Core;

use Sonder\Enums\HttpMethodsEnum;
use Sonder\Exceptions\AppException;
use Sonder\Exceptions\RequestObjectException;
use Sonder\Interfaces\IRequestObject;
use Sonder\Interfaces\IHttpMethodsEnum;
use Sonder\Interfaces\IUserModel;
use Sonder\Exceptions\CoreException;
use Sonder\Plugins\Session\Interfaces\ISessionPlugin;

#[IRequestObject]
final class RequestObject implements IRequestObject
{
    private const DEFAULT_URL = '/';

    private const DEFAULT_LANGUAGE = 'en';

    /**
     * @var IHttpMethodsEnum
     */
    #[IHttpMethodsEnum]
    private readonly IHttpMethodsEnum $_httpMethod;

    /**
     * @var array
     */
    private array $_urlValues = [];

    /**
     * @var array
     */
    private array $_postValues = [];

    /**
     * @var array|null
     */
    private ?array $_apiValues = [];

    /**
     * @var array|null
     */
    private ?array $_cliValues = [];

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
     * @var ISessionPlugin|null
     */
    #[ISessionPlugin]
    private ?ISessionPlugin $_session = null;

    /**
     * @var IUserModel|null
     */
    private ?IUserModel $_user = null;

    /**
     * @var string|null
     */
    private ?string $_csrfToken = null;

    /**
     * @var string|null
     */
    private ?string $_controller = null;

    /**
     * @var string|null
     */
    private ?string $_controllerMethod = null;

    /**
     * @var bool
     */
    private bool $_noCache = false;

    /**
     * @var int|null
     */
    private ?int $_time = null;

    /**
     * @var string
     */
    private string $_language;

    /**
     * @throws RequestObjectException
     */
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
     * @return IHttpMethodsEnum
     */
    final public function getHttpMethod(): IHttpMethodsEnum
    {
        return $this->_httpMethod;
    }

    /**
     * @return string
     * @throws RequestObjectException
     */
    final public function getHost(): string
    {
        if (!empty($this->_host)) {
            return $this->_host;
        }

        throw new RequestObjectException(
            RequestObjectException::MESSAGE_REQUEST_HOST_NOT_SET,
            AppException::CODE_REQUEST_HOST_NOT_SET
        );
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
     * @throws RequestObjectException
     */
    final public function getFullUrl(): string
    {
        $host = $this->getHost();
        $url = $this->getUrl();

        return sprintf('%s%s', $host, $url);
    }

    /**
     * @return array
     */
    final public function getUrlValues(): array
    {
        return $this->_urlValues;
    }

    /**
     * @param string $valueName
     * @return string|null
     */
    final public function getUrlValue(string $valueName): ?string
    {
        $urlValue = $this->_urlValues[$valueName] ?? null;

        return empty($urlValue) ? null : (string)$urlValue;
    }

    /**
     * @return array
     */
    final public function getPostValues(): array
    {
        return $this->_postValues;
    }

    /**
     * @param string $valueName
     * @return string|null
     */
    final public function getPostValue(string $valueName): ?string
    {
        $postValue = $this->_postValues[$valueName] ?? null;

        return empty($postValue) ? null : (string)$postValue;
    }

    /**
     * @return array|null
     */
    final public function getApiValues(): ?array
    {
        return $this->_apiValues;
    }

    /**
     * @param string $valueName
     * @return string|null
     */
    final public function getApiValue(string $valueName): ?string
    {
        $apiValue = $this->_apiValues[$valueName] ?? null;

        return empty($apiValue) ? null : (string)$apiValue;
    }

    /**
     * @return array|null
     */
    final public function getCliValues(): ?array
    {
        return $this->_cliValues;
    }

    /**
     * @param string|null $valueName
     * @return string|null
     */
    final public function getCliValue(?string $valueName = null): ?string
    {
        $cliValue = $this->_cliValues[$valueName] ?? null;

        return empty($cliValue) ? null : (string)$cliValue;
    }

    /**
     * @return string
     */
    final public function getLanguage(): string
    {
        return $this->_language;
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
     * @return IUserModel|null
     */
    final public function getUser(): ?IUserModel
    {
        return $this->_user;
    }

    /**
     * @return string|null
     */
    final public function getCsrfToken(): ?string
    {
        return $this->_csrfToken;
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
    final public function getControllerMethod(): ?string
    {
        return $this->_controllerMethod;
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
     * @return void
     */
    final public function setIp(?string $ip = null): void
    {
        if (!empty($ip)) {
            $this->_ip = $ip;
        }
    }

    /**
     * @param array|null $urlValues
     * @return void
     */
    final public function setUrlValues(?array $urlValues = null): void
    {
        $this->_urlValues = $urlValues ?? [];
    }

    /**
     * @param array|null $postValues
     * @return void
     */
    final public function setPostValues(?array $postValues = null): void
    {
        $this->_postValues = $postValues ?? [];
    }

    /**
     * @param string|null $language
     * @return void
     */
    final public function setLanguage(?string $language = null): void
    {
        $language = empty($language) ? $this->_getDefaultLanguage() : $language;

        $this->_language = $language;
    }

    /**
     * @param array|null $apiValues
     * @return void
     */
    final public function setApiValues(?array $apiValues = null): void
    {
        $this->_apiValues = $apiValues ?? [];
    }

    /**
     * @param array|null $cliValues
     * @return void
     */
    final public function setCliValues(?array $cliValues = null): void
    {
        $this->_cliValues = $cliValues ?? [];
    }

    /**
     * @return void
     * @throws CoreException
     */
    final public function setSession(): void
    {
        /* @var ISessionPlugin $sessionPlugin */
        $sessionPlugin = CoreObject::getPlugin('session');

        $this->_session = $sessionPlugin;
    }

    /**
     * @param IUserModel $user
     * @return void
     */
    final public function setUser(IUserModel $user): void
    {
        $this->_user = $user;
    }

    /**
     * @param string|null $csrfToken
     * @return void
     */
    final public function setCsrfToken(?string $csrfToken = null): void
    {
        $this->_csrfToken = $csrfToken;
    }

    /**
     * @param string|null $controller
     * @return void
     */
    final public function setController(?string $controller = null): void
    {
        $this->_controller = $controller;
    }

    /**
     * @param string|null $controllerMethod
     * @return void
     */
    final public function setControllerMethod(
        ?string $controllerMethod = null
    ): void {
        $this->_controllerMethod = $controllerMethod;
    }

    /**
     * @param bool $noCache
     * @return void
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

    /**
     * @return void
     * @throws RequestObjectException
     */
    private function _setHttpMethod(): void
    {
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $httpMethod = mb_convert_case($httpMethod, MB_CASE_LOWER);
        $httpMethod = HttpMethodsEnum::tryFrom($httpMethod);

        if (!empty($httpMethod)) {
            $this->_httpMethod = $httpMethod;

            return;
        }

        throw new RequestObjectException(
            RequestObjectException::MESSAGE_REQUEST_UNSUPPORTED_HTTP_METHOD,
            AppException::CODE_REQUEST_UNSUPPORTED_HTTP_METHOD
        );
    }

    /**
     * @return void
     */
    private function _setUrl(): void
    {
        $this->_url = $_SERVER['REQUEST_URI'];
    }

    /**
     * @return void
     */
    private function _setHost(): void
    {
        $protocol = 'http';

        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
            $protocol = 'https';
        }

        $this->_host = sprintf('%s://%s', $protocol, $_SERVER['HTTP_HOST']);
    }

    /**
     * @return void
     */
    private function _setUserAgent(): void
    {
        $this->_userAgent = $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * @return void
     */
    private function _setTime(): void
    {
        $this->_time = time();
    }

    /**
     * @return void
     */
    private function _removeGlobalInputValues(): void
    {
        $_GET = [];
        $_POST = [];
        $_REQUEST = [];
    }
}
