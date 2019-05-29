<?php
interface IPushResponse
{
    public function getStatus() : bool;

    public function getErrorMessage() : string;

    public function getRecipientsCount() : int;

    public function getRemoteCode() : string;
}
?>