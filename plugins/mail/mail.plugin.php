<?php
use PHPMailer\PHPMailer\Exception;

class MailPlugin
{
    private $_provider = NULL;

    public function setProvider(string $providerIdent = '') : void
    {
        $credentialsProviderClass = $providerIdent.'Credentials';
        $credentialsProviderFile  = __DIR__.'/providers/'.$providerIdent.'/'
                                    .$credentialsProviderClass.'.php';

        $responseProviderClass = $providerIdent.'Response';
        $responseProviderFile  = __DIR__.'/providers/'.$providerIdent.'/'
                                 .$responseProviderClass.'.php';

        $providerClass = $providerIdent.'Provider';
        $providerFile  = __DIR__.'/providers/'.$providerIdent.'/'
                         .$providerClass.'.php';

        if (
            !file_exists($credentialsProviderFile) ||
            !is_file($credentialsProviderFile)
        ) {
            throw new Exception('Invalid Mail Provider');
        }

        if (
            !file_exists($responseProviderFile) ||
            !is_file($responseProviderFile)
        ) {
            throw new Exception('Invalid Mail Provider');
        
        }

        if (!file_exists($providerFile) || !is_file($providerFile)) {
            throw new Exception('Invalid Mail Provider');
        }
        
        include_once $credentialsProviderFile;
        include_once $responseProviderFile;
        include_once $providerFile;

        if (!class_exists($credentialsProviderClass)) {
            throw new Exception('Invalid Mail Provider');
        }

        if (!class_exists($responseProviderClass)) {
            throw new Exception('Invalid Mail Provider');
        }

        if (!class_exists($providerClass)) {
            throw new Exception('Invalid Mail Provider');
        }

        $this->_provider = new $providerClass($providerIdent);
    }

    public function send(
        ?string $email      = NULL,
        ?string $message    = NULL,
        ?string $subject    = NULL,
        ?string $replyEmail = NULL,
        ?string $senderName = NULL
    ) : IMailResponse
    {
        if ($this->_provider === NULL) {
            throw new Exception('Mail Provider Is Not Set');
        }

        if (empty($email)) {
            throw new Exception('Email Is Not Set');
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

        if (!empty($replyEmail) && !$this->_isValidEmailFormat($replyEmail)) {
            throw new Exception('Email For Replies Has Bad Format');
        }

        if (empty($message)) {
            throw new Exception('Message Is Not Set');
        }

        try {
            $response = $this->_provider->send(
                $email,
                $message,
                $subject,
                $replyEmail,
                $senderName
            );
        } catch (Exception $exp) {
            $errorMessage = $exp->getMessage();

            $this->_handleSendingError(
                $email,
                $message,
                $subject,
                $senderName,
                $replyEmail,
                $errorMessage
            );

            throw new Exception($errorMessage);
        }

        if (!$response->getStatus()) {
            $this->_handleSendingError(
                $email,
                $message,
                $subject,
                $senderName,
                $replyEmail,
                $response->getErrorMesage()
            );
        }

        return $response;
    }

    private function _handleSendingError(
        ?string $email        = NULL,
        ?string $message      = NULL,
        ?string $subject      = NULL,
        ?string $senderName   = NULL,
        ?string $replyEmail   = NULL,
        ?string $errorMessage = NULL
    ) : void
    {
        $failedMailData = [
            'email'       => $email,
            'subject'     => $subject,
            'message'     => $message,
            'sender_name' => $senderName,
            'reply_email' => $replyEmail,
            'error'       => $errorMessage,
            'time'        => date('Y:m:d H:i:s')
        ];

        $failedMailLogEntry = json_encode($failedMailData)."\n";
        $failedMailLogEntry = '['.date('Y-m-d H:i:s').'] '.$failedMailLogEntry;

        $failedMailLogDirPath = __DIR__.'/../../../res/logs/mail/';
        $failedMailLogFilePath = $failedMailLogDirPath.
                                 'mail-'.date('Y-m-d').'.log';
        if (
            !file_exists($failedMailLogDirPath) ||
            !is_dir($failedMailLogDirPath)
        ) {
            mkdir($failedMailLogDirPath);
            chmod($failedMailLogDirPath, 0775);
        }

        if (
            !file_exists($failedMailLogFilePath) ||
            !is_file($failedMailLogFilePath)
        ) {
            touch($failedMailLogFilePath);
            chmod($failedMailLogFilePath, 0775);
        }

        $failedMailLogFile = fopen($failedMailLogFilePath, 'a');

        fwrite($failedMailLogFile, $failedMailLogEntry);
        fclose($failedMailLogFile);

        $oldYear = strval(intval(date('Y'))-1);

        $oldFailedMailLogFilePath = $failedMailLogDirPath.'/mail-'.$oldYear.'-'.
                                    date('m-d').'.log';
        if (
            file_exists($oldFailedMailLogFilePath) ||
            is_file($oldFailedMailLogFilePath)
        ) {
            unlink($oldFailedMailLogFilePath);
        }
    }

    private function _isValidEmailFormat(string $email = '') : bool
    {
        return preg_match('/^(.*?)@(.*?)\.(.*?)$/su', $email);
    }
}
?>