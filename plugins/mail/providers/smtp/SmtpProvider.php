<?php

namespace Sonder\Plugins\Mail\Providers\Smtp;

use Exception;
use PHPMailer\PHPMailer;
use Sonder\Plugins\Mail\Interfaces\IMailResponse;
use Sonder\Plugins\Mail\Providers\AbstractMailProvider;

final class SmtpProvider extends AbstractMailProvider
{
    const DEFAULT_SUBJECT = 'Subject not set';

    /**
     * @param string|null $providerIdent
     *
     * @throws Exception
     */
    final public function __construct(?string $providerIdent = null)
    {
        parent::__construct($providerIdent);
    }

    /**
     * @param string $email
     * @param string $message
     * @param string|null $subject
     * @param string|null $replyEmail
     * @param string|null $senderName
     *
     * @return IMailResponse
     *
     * @throws Exception
     */
    final public function send(
        string  $email,
        string  $message,
        ?string $subject = null,
        ?string $replyEmail = null,
        ?string $senderName = null
    ): IMailResponse
    {
        if (empty($subject)) {
            $subject = SmtpProvider::DEFAULT_SUBJECT;
        }

        if (empty($replyEmail)) {
            $replyEmail = $this->credentials->getReplyEmail();
        }

        if (empty($senderName)) {
            $senderName = $this->credentials->getSenderName();
        }

        $email = mb_convert_case($email, MB_CASE_LOWER);
        $replyEmail = mb_convert_case($replyEmail, MB_CASE_LOWER);

        $email = preg_replace('/\s+/su', '', $email);
        $replyEmail = preg_replace('/\s+/su', '', $replyEmail);
        $subject = preg_replace('/\s+/su', ' ', $subject);
        $subject = preg_replace('/(^\s)|(\s$)/su', '', $subject);
        $senderName = preg_replace('/\s+/su', ' ', $senderName);
        $senderName = preg_replace('/(^\s)|(\s$)/su', '', $senderName);

        if (!$this->_isValidEmailFormat($email)) {
            throw new Exception('Email Has Bad Format');
        }

        if (!$this->_isValidEmailFormat($replyEmail)) {
            throw new Exception('Email (For Replies) Has Bad Format');
        }

        $mail = new PHPMailer\PHPMailer(true);
        $mail->IsSMTP();
        $mail->IsHTML(true);

        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';

        $mail->Host = $this->credentials->getServerAddress();
        $mail->Port = $this->credentials->getServerPort();
        $mail->Username = $this->credentials->getLogin();
        $mail->Password = $this->credentials->getPassword();

        $mail->AddAddress($email);
        $mail->Body = $message;
        $mail->AltBody = strip_tags($message);
        $mail->Subject = $subject;

        $mail->From = $replyEmail;
        $mail->FromName = $senderName;

        $this->response->setStatusSuccess();

        if (!$mail->Send()) {
            $this->response->setStatusFail();
            $this->response->setErrorMessage($mail->ErrorInfo);
        }

        return $this->response;
    }

    /**
     * @param string|null $email
     *
     * @return bool
     */
    private function _isValidEmailFormat(?string $email = null): bool
    {
        return preg_match('/^(.*?)@(.*?)\.(.*?)$/su', (string)$email);
    }
}
