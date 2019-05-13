<?php
interface IPushCredentials
{
    public function getURL() : string;

    public function getAppID() : string;

    public function getAPIKey() : string;

    public function getOptions() : array;

    public function setURL(string $url = '#') : void;

    public function setAppID(string $appID = '') : void;

    public function setAPIKey(string $APIKey = '') : void;

    public function setOptions(array $options = []) : void;
}
?>
