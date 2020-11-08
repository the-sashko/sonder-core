<?php
use PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class SmtpProvider extends AbstractMailProvider
{
    const DEFAULT_SUBJECT = 'Subject not set';

    public function __construct(?string $providerIdent = null)
    {
        parent::__construct($providerIdent);

        require_once __DIR__.'/autoload.php';
    }

    public function send(
        string  $email,
        string  $message,
        ?string $subject    = null,
        ?string $replyEmail = null,
        ?string $senderName = null
    ): IMailResponse
    {
        if (empty($subject)) {
            $subject = static::DEFAULT_SUBJECT;
        }

        if (empty($replyEmail)) {
            $replyEmail = $this->credentials->getReplyEmail();
        }

        if (empty($senderName)) {
            $senderName = $this->credentials->getSenderName();
        }

        $email      = mb_convert_case($email, MB_CASE_LOWER);
        $replyEmail = mb_convert_case($replyEmail, MB_CASE_LOWER);

        $email      = preg_replace('/\s+/su', '', $email);
        $replyEmail = preg_replace('/\s+/su', '', $replyEmail);
        $subject    = preg_replace('/\s+/su', ' ', $subject);
        $subject    = preg_replace('/(^\s)|(\s$)/su', '', $subject);
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

        $mail->SMTPAuth   = true;
        $mail->SMTPSecure = 'ssl';

        $mail->Host     = $this->credentials->getServerAddress();
        $mail->Port     = $this->credentials->getServerPort();
        $mail->Username = $this->credentials->getLogin();
        $mail->Password = $this->credentials->getPassword();

        $mail->AddAddress($email);
        $mail->Body    = $message;
        $mail->AltBody = strip_tags($message);
        $mail->Subject = $subject;

        $mail->From     = $replyEmail;
        $mail->FromName = $senderName;

        $this->response->setStatusSuccess();

        if (!$mail->Send()) {
            $this->response->setStatusFail();
            $this->response->setErrorMessage($mail->ErrorInfo);
        }

        return $this->response;
    }

    private function _isValidEmailFormat(?string $email = null): bool
    {
        return preg_match('/^(.*?)@(.*?)\.(.*?)$/su', (string) $email);
    }
}
