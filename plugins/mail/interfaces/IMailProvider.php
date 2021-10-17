<?php

namespace Sonder\Plugins\Mail\Interfaces;

interface IMailProvider
{
    /**
     * @param string $email
     * @param string $message
     * @param string|null $subject
     * @param string|null $replyEmail
     * @param string|null $senderName
     *
     * @return IMailResponse
     */
    public function send(
        string  $email,
        string  $message,
        ?string $subject,
        ?string $replyEmail,
        ?string $senderName
    ): IMailResponse;
}
