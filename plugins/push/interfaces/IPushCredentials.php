<?php
interface IPushCredentials
{
    public function getUrl(): ?string;

    public function getLogin(): ?string;

    public function getToken(): ?string;

    public function getSubscribersGroup(): ?string;

    public function getOptions(): ?array;

    public function getDefaultMessageURL(): ?string;

    public function getDefaultMessageTitle(): ?string;

    public function getDefaultMessageImage(): ?string;

    public function setUrl(?string $url = null): void;

    public function setLogin(?string $login = null): void;

    public function setToken(?string $token = null): void;

    public function setSubscribersGroup(
        ?string $subscribersGroup = null
    ): void;

    public function setOptions(?array $options = null): void;

    public function setDefaultMessageUrl(?string $url = null): void;

    public function setDefaultMessageTitle(?string $title = null): void;

    public function setDefaultMessageImage(?string $image = null): void;
}
