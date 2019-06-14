<?php
interface ISMSProvider
{
    public function sendMessage(
        string $phone   = '',
        string $message = ''
    ) : ISMSResponse;

    public function checkMessage(
        ISMSResponse $smsResponse = NULL
    ) : ISMSResponse;
}
?>