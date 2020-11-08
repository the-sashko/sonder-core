<?php
interface IPushCredentials
{
    public function getURL(): string;

    public function getLogin(): string;

    public function getToken(): string;

    public function getSubscribersGroup(): string;

    public function getOptions(): array;

    public function getDefaultMessageURL(): string;

    public function getDefaultMessageTitle(): string;

    public function getDefaultMessageImage(): string;

    public function setURL(string $url = '#'): void;

    public function setLogin(string $login = ''): void;

    public function setToken(string $token = ''): void;

    public function setSubscribersGroup(string $subscribersGroup = ''): void;

    public function setOptions(array $options = []): void;

    public function setDefaultMessageURL(string $url = '#'): void;

    public function setDefaultMessageTitle(string $title = ''): void;

    public function setDefaultMessageImage(string $image = ''): void;
}
