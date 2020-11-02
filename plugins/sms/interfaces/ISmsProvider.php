<?php
interface ISmsProvider
{
    public function sendMessage(
        ?string $phone   = null,
        ?string $message = null
    ): ?ISmsResponse;

    public function checkMessage(
        ?ISMSResponse $smsResponse = null
    ): ?ISmsResponse;
}
