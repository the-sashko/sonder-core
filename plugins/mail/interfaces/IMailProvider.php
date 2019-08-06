<?php
interface IMailProvider
{
    public function send(
        string  $email,
        string  $message,
        ?string $subject,
        ?string $replyEmail,
        ?string $senderName
    ) : IMailResponse;
}
?>
