<?php
interface IMailResponse
{
    public function getStatus() : bool;

    public function getErrorMessage() : ?string;
}
?>
