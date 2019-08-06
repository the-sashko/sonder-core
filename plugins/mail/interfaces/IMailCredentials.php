<?php
interface IMailCredentials
{
    public function getServerAddress() : string;

    public function getServerPort() : int;

    public function getLogin() : string;

    public function getPassword() : string;

    public function getReplyEmail() : string;

    public function getSenderName() : string;
}
?>
