<?php
interface ISMSCredentials
{
    public function getLogin() : string;

    public function getPassword() : string;

    public function getURL() : string;

    public function getAlphaName() : string;

    public function getOptions() : array;

    public function setLogin(string $login = '') : void;

    public function setPassword(string $password = '') : void;

    public function setURL(string $url = '#') : void;

    public function setAlphaName(string $alphaName = '') : void;

    public function setOptions(array $options = []) : void;
}
?>