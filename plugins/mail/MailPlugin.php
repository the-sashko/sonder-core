<?php

namespace Sonder\Plugins;

use Exception;
use Sonder\Plugins\Mail\Interfaces\IMailProvider;
use Sonder\Plugins\Mail\Interfaces\IMailResponse;
use Throwable;

final class MailPlugin
{
    private const LOG_DIR_PATH = __DIR__ . '/../../../logs/mail';

    /**
     * @var IMailProvider
     */
    private IMailProvider $_provider;

    /**
     * @param string|null $providerIdent
     *
     * @throws Exception
     */
    final public function setProvider(?string $providerIdent = null): void
    {
        if (empty($providerIdent)) {
            throw new Exception('Provider Is Not Set');
        }

        $credentialsProviderClass = sprintf('%sCredentials', $providerIdent);

        $credentialsProviderFile = __DIR__ . '/providers/%s/%s.php';

        $credentialsProviderFile = sprintf(
            $credentialsProviderFile,
            $providerIdent,
            $credentialsProviderClass
        );

        $responseProviderClass = sprintf('%sResponse', $providerIdent);

        $responseProviderFile = __DIR__ . '/providers/%s/%s.php';

        $responseProviderFile = sprintf(
            $responseProviderFile,
            $providerIdent,
            $responseProviderClass
        );

        $providerClass = sprintf('%sProvider', $providerIdent);

        $providerFile = __DIR__ . '/providers/%s/%s.php';

        $providerFile = sprintf($providerFile, $providerIdent, $providerClass);

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

        $this->_provider = new $providerClass($providerIdent);
    }

    /**
     * @param string|null $email
     * @param string|null $message
     * @param string|null $subject
     * @param string|null $replyEmail
     * @param string|null $senderName
     *
     * @return IMailResponse
     *
     * @throws Exception
     */
    final public function send(
        ?string $email = null,
        ?string $message = null,
        ?string $subject = null,
        ?string $replyEmail = null,
        ?string $senderName = null
    ): IMailResponse
    {
        if ($this->_provider === null) {
            throw new Exception('Mail Provider Is Not Set');
        }

        if (empty($email)) {
            throw new Exception('Email Is Not Set');
        }

        $email = mb_convert_case($email, MB_CASE_LOWER);
        $replyEmail = mb_convert_case((string)$replyEmail, MB_CASE_LOWER);

        $email = preg_replace('/\s+/su', '', $email);
        $replyEmail = preg_replace('/\s+/su', '', $replyEmail);
        $subject = preg_replace('/\s+/su', ' ', (string)$subject);
        $subject = preg_replace('/(^\s)|(\s$)/su', '', $subject);
        $senderName = preg_replace('/\s+/su', ' ', (string)$senderName);
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
        } catch (Throwable $thr) {
            $errorMessage = $thr->getMessage();

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
                $response->getErrorMessage()
            );
        }

        return $response;
    }

    /**
     * @param string|null $email
     * @param string|null $message
     * @param string|null $subject
     * @param string|null $senderName
     * @param string|null $replyEmail
     * @param string|null $errorMessage
     */
    private function _handleSendingError(
        ?string $email = null,
        ?string $message = null,
        ?string $subject = null,
        ?string $senderName = null,
        ?string $replyEmail = null,
        ?string $errorMessage = null
    ): void
    {
        $failedMailData = [
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
            'sender_name' => $senderName,
            'reply_email' => $replyEmail,
            'error' => $errorMessage,
            'time' => date('Y:m:d H:i:s')
        ];

        $failedMailLogEntry = sprintf(
            '%s %s%s',
            date('[Y-m-d H:i:s]'),
            json_encode($failedMailData),
            "\n"
        );

        $failedMailLogDirPath = MailPlugin::LOG_DIR_PATH;

        if (defined('APP_PROTECTED_DIR_PATH')) {
            $failedMailLogDirPath = sprintf(
                '%s/logs/mail',
                APP_PROTECTED_DIR_PATH
            );
        }

        $failedMailLogFilePath = sprintf(
            '%s/mail-%s.log',
            $failedMailLogDirPath,
            date('Y-m-d')
        );

        if (
            !file_exists($failedMailLogDirPath) ||
            !is_dir($failedMailLogDirPath)
        ) {
            mkdir($failedMailLogDirPath, 0775, true);
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

        $oldYear = strval(intval(date('Y')) - 1);

        $oldFailedMailLogFilePath = sprintf(
            '%s/mail-%s-%s.log',
            $failedMailLogDirPath,
            $oldYear,
            date('m-d')
        );

        if (
            file_exists($oldFailedMailLogFilePath) ||
            is_file($oldFailedMailLogFilePath)
        ) {
            unlink($oldFailedMailLogFilePath);
        }
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
