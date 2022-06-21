<?php

namespace Sonder\Interfaces;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
interface IRequestObject
{
    /**
     * @return IHttpMethodsEnum
     */
    public function getHttpMethod(): IHttpMethodsEnum;

    /**
     * @return string
     */
    public function getHost(): string;

    /**
     * @return string
     */
    public function getUrl(): string;

    /**
     * @return string
     */
    public function getFullUrl(): string;

    /**
     * @return array|null
     */
    public function getUrlValues(): ?array;

    /**
     * @param string $valueName
     * @return string|null
     */
    public function getUrlValue(string $valueName): ?string;

    /**
     * @return array|null
     */
    public function getPostValues(): ?array;

    /**
     * @param string $valueName
     * @return string|null
     */
    public function getPostValue(string $valueName): ?string;

    /**
     * @return array|null
     */
    public function getApiValues(): ?array;

    /**
     * @param string $valueName
     * @return string|null
     */
    public function getApiValue(string $valueName): ?string;

    /**
     * @return array|null
     */
    public function getCliValues(): ?array;

    /**
     * @param string $valueName
     * @return string|null
     */
    public function getCliValue(string $valueName): ?string;

    /**
     * @return string
     */
    public function getLanguage(): string;

    /**
     * @return string|null
     */
    public function getIp(): ?string;

    /**
     * @return string|null
     */
    public function getUserAgent(): ?string;

    /**
     * @return object|null
     */
    public function getSession(): ?object;

    /**
     * @return IUserModel|null
     */
    public function getUser(): ?IUserModel;

    /**
     * @return string|null
     */
    public function getCsrfToken(): ?string;

    /**
     * @return string|null
     */
    public function getController(): ?string;

    /**
     * @return string|null
     */
    public function getControllerMethod(): ?string;

    /**
     * @return bool
     */
    public function getNoCache(): bool;

    /**
     * @return int|null
     */
    public function getTime(): ?int;

    /**
     * @param string|null $ip
     * @return void
     */
    public function setIp(?string $ip = null): void;

    /**
     * @param array|null $urlValues
     * @return void
     */
    public function setUrlValues(?array $urlValues = null): void;

    /**
     * @param array|null $postValues
     * @return void
     */
    public function setPostValues(?array $postValues = null): void;

    /**
     * @param string|null $language
     * @return void
     */
    public function setLanguage(?string $language = null): void;

    /**
     * @param array|null $apiValues
     * @return void
     */
    public function setApiValues(?array $apiValues = null): void;

    /**
     * @param array|null $cliValues
     * @return void
     */
    public function setCliValues(?array $cliValues = null): void;

    /**
     * @return void
     */
    public function setSession(): void;

    /**
     * @param IUserModel $user
     * @return void
     */
    public function setUser(IUserModel $user): void;

    /**
     * @param string|null $csrfToken
     * @return void
     */
    public function setCsrfToken(?string $csrfToken = null): void;

    /**
     * @param string|null $controller
     * @return void
     */
    public function setController(?string $controller = null): void;

    /**
     * @param string|null $controllerMethod
     * @return void
     */
    public function setControllerMethod(?string $controllerMethod = null): void;

    /**
     * @param bool $noCache
     * @return void
     */
    public function setNoCache(bool $noCache = false): void;
}
