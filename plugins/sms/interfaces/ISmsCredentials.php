<?php
interface ISmsCredentials
{
    public function getLogin(): ?string;

    public function getToken(): ?string;

    public function getUrl(): ?string;

    public function getAlphaName(): ?string;

    public function getOptions(): ?array;

    public function setLogin(?string $login = null): void;

    public function setToken(?string $token = null): void;

    public function setUrl(?string $url = null): void;

    public function setAlphaName(?string $alphaName = null) : void;

    public function setOptions(?array $options = null): void;
}
