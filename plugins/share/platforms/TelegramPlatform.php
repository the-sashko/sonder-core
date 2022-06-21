<?php

namespace Sonder\Plugins\Share\Platforms;

use Sonder\Plugins\Share\Exceptions\ShareException;
use Sonder\Plugins\Share\Exceptions\SharePlatformException;
use Sonder\Plugins\Share\Interfaces\ISharePlatform;

final class TelegramPlatform extends AbstractPlatform implements ISharePlatform
{
    private const TELEGRAM_API_URL = 'https://api.telegram.org';

    /**
     * @param string $message
     *
     * @return bool
     *
     * @throws SharePlatformException
     */
    final public function send(string $message): bool
    {
        $this->_checkCredentials();

        $message = $this->_getFormattedMessage($message);

        if (strlen($message) < 3) {
            $errorMessage = sprintf(
                '%s: Message Too Short',
                SharePlatformException::MESSAGE_MESSAGE_HAS_BAD_FORMAT
            );

            throw new SharePlatformException(
                $errorMessage,
                ShareException::CODE_MESSAGE_HAS_BAD_FORMAT
            );
        }

        if (!array_key_exists('chats', $this->credentials)) {
            $errorMessage = sprintf(
                '%s: Chat List Is Not Set',
                SharePlatformException::MESSAGE_INVALID_CREDENTIALS
            );

            throw new SharePlatformException(
                $errorMessage,
                ShareException::CODE_INVALID_CREDENTIALS
            );
        }

        foreach ($this->credentials['chats'] as $chatCode) {
            if (!empty($chatCode)) {
                $this->_sendToChat($message, $chatCode);
            }
        }

        return true;
    }

    /**
     * @param string|null $message
     *
     * @return string
     */
    private function _getFormattedMessage(?string $message = null): string
    {
        $message = (string) $message;

        $message = strip_tags($message);
        $message = htmlspecialchars_decode($message);

        $message = preg_replace('/\n+/su', '<br>', $message);
        $message = preg_replace('/\s+/su', ' ', $message);
        $message = preg_replace('/(\s<br>)|(<br>\s)/su', '<br>', $message);
        $message = preg_replace('/<br>/su', "\n", $message);
        $message = preg_replace('/\n+/su', "\n", $message);
        $message = preg_replace('/(^\s)|(\s$)/su', '', $message);
        $message = preg_replace('/\n/su', "\n\n", $message);
        $message = urlencode($message);

        return str_replace('&quot;', '---', $message);
    }

    /**
     * @param string $message
     * @param string $chatCode
     *
     * @throws SharePlatformException
     */
    private function _sendToChat(string $message, string $chatCode): void
    {
        $url = $this->_getApiUrl();

        $url = sprintf(
            '%s?parse_mode=Markdown&chat_id=%s&text=%s',
            $url,
            $chatCode,
            $message
        );

        $this->_sendToRemoteApi($url);
    }

    /**
     * @param string $url
     *
     * @throws SharePlatformException
     */
    private function _sendToRemoteApi(string $url): void
    {
        $curl        = curl_init();
        $curlHeaders = $this->_getCurlHeaders($url);

        curl_setopt_array($curl, $curlHeaders);

        $curlResponse = curl_exec($curl);
        $curlError    = curl_error($curl);

        curl_close($curl);

        if ($curlError) {
            $errorMessage = sprintf('%s (Curl Error)', $curlError);

            $errorMessage = sprintf(
                '%s: %s',
                SharePlatformException::MESSAGE_REMOTE_ERROR,
                $errorMessage
            );

            throw new SharePlatformException(
                $errorMessage,
                ShareException::CODE_REMOTE_ERROR
            );
        }

        $this->_validateApiResponse($curlResponse);
    }

    /**
     * @param string|null $apiResponseJson
     *
     * @throws SharePlatformException
     */
    private function _validateApiResponse(
        ?string $apiResponseJson = null
    ): void
    {
        if (empty($apiResponseJson)) {
            $errorMessage = sprintf(
                '%s: Invalid Telegram API Response',
                SharePlatformException::MESSAGE_REMOTE_ERROR
            );

            throw new SharePlatformException(
                $errorMessage,
                ShareException::CODE_REMOTE_ERROR
            );
        }

        $apiResponse = (array) json_decode($apiResponseJson, true);

        if (!array_key_exists('ok', $apiResponse)) {
            $errorMessage = sprintf(
                '%s (Telegram API Error)',
                $apiResponseJson
            );

            $errorMessage = sprintf(
                '%s: %s',
                SharePlatformException::MESSAGE_REMOTE_ERROR,
                $errorMessage
            );

            throw new SharePlatformException(
                $errorMessage,
                ShareException::CODE_REMOTE_ERROR
            );
        }

        $apiResponse['ok'] = (bool) $apiResponse['ok'];

        if (!$apiResponse['ok']) {
            $errorMessage = sprintf(
                '%s (Telegram API Error)',
                $apiResponseJson
            );

            $errorMessage = sprintf(
                '%s: %s',
                SharePlatformException::MESSAGE_REMOTE_ERROR,
                $errorMessage
            );

            throw new SharePlatformException(
                $errorMessage,
                ShareException::CODE_REMOTE_ERROR
            );
        }
    }

    /**
     * @param string $url
     *
     * @return array
     */
    private function _getCurlHeaders(string $url): array
    {
        return [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_POSTFIELDS     => '',
            CURLOPT_FAILONERROR    => false
        ];
    }

    /**
     * @return string
     */
    private function _getApiUrl(): string
    {
        $token = null;

        if (array_key_exists('token', $this->credentials)) {
            $token = $this->credentials['token'];
        }

        return sprintf(
            '%s/bot%s/sendMessage',
            TelegramPlatform::TELEGRAM_API_URL,
            (string) $token
        );
    }

    /**
     * @throws SharePlatformException
     */
    private function _checkCredentials(): void
    {
        if (
            !array_key_exists('token', $this->credentials) ||
            !array_key_exists('chats', $this->credentials) ||
            !is_array($this->credentials['chats'])
        ) {
            throw new SharePlatformException(
                SharePlatformException::MESSAGE_INVALID_CREDENTIALS,
                ShareException::CODE_INVALID_CREDENTIALS
            );
        }

        if (strlen($this->credentials['token']) < 1) {
            $errorMessage = sprintf(
                '%s: Token Is Not Set',
                SharePlatformException::MESSAGE_INVALID_CREDENTIALS
            );

            throw new SharePlatformException(
                $errorMessage,
                ShareException::CODE_INVALID_CREDENTIALS
            );
        }

        if (empty($this->_credentials['chats'])) {
            $errorMessage = sprintf(
                '%s: Chat List Is Not Set',
                SharePlatformException::MESSAGE_INVALID_CREDENTIALS
            );

            throw new SharePlatformException(
                $errorMessage,
                ShareException::CODE_INVALID_CREDENTIALS
            );
        }
    }
}
