<?php
interface ISMSProvider
{
    public function sendMessage(
        string $phone   = '',
        string $message = ''
    ) : ISMSResponse;
}
?>