<?php
/**
 * Plugin For Sending Messages To Telegram
 */
class TelegramPlugin
{
    /**
     * @var string Telegram API URL
     */
    const TELEGRAM_API_URL = 'https://api.telegram.org';

    /**
     * @var array|null Telegram API Credentials
     */
    private $_credentials = null;

    /**
     * Set Telegram API Credentials
     *
     * @param array $credentials Telegram API Credentials
     */
    public function setCredentials(array $credentials): void
    {
        $this->_credentials = $credentials;
    }

    /**
     * Sending Message To Telegram Usera, Chats Or Channel
     *
     * @param string|null $message Message Text Value
     */
    public function send(?string $message = null): void
    {
        if (empty($message)) {
            throw new \Exception('Message Is Not Set');
        }

        $this->_checkCredentials();

        $message = $this->_getFomatedMessage($message);

        if (strlen($message) < 3) {
            throw new \Exception('Message Too Short');
        }

        if (!array_key_exists('chats', $this->_credentials)) {
            throw new \Exception('Chat List Is Not Set');
        }

        foreach ($this->_credentials['chats'] as $chatCode) {
            if (!empty($chatCode)) {
                $this->_sendToChat($message, $chatCode);
            }
        }
    }

    /**
     * Removing From Message Text Tags And Extra Spaces
     *
     * @param string|null $message Input Message Text Value
     *
     * @return string Output Message Text Value
     */
    private function _getFomatedMessage(?string $message = null): string
    {
        $message = (string) $message;

        $message = strip_tags($message);
        $message = htmlspecialchars($message);
        $message = addslashes($message);

        $message = preg_replace('/\n+/su', '<br>', $message);
        $message = preg_replace('/\s+/su', ' ', $message);
        $message = preg_replace('/(\s<br>)|(<br>\s)/su', '<br>', $message);
        $message = preg_replace('/<br>/su', "\n", $message);
        $message = preg_replace('/\n+/su', "\n", $message);
        $message = preg_replace('/(^\s)|(\s$)/su', '', $message);
        $message = urlencode($message);

        return $message;
    }

    /**
     * Send Message To Chat, Channel Or User By ID
     *
     * @param string $message  Message
     * @param string $chatCode ID Of Chat Or Channel
     */
    private function _sendToChat(string $message, string $chatCode): void
    {
        $url = $this->_getApiUrl();
        $url = sprintf('%s?chat_id=%s&text=', $url, $chatCode, $message);

        $this->_sendToRemoteApi($url);
    }

    /**
     * Send Request To API URL
     *
     * @param string $url API URL
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
            throw new \Exception(sprintf('Curl Error: %s', $curlError));
        }

        $this->_validateApiResponse($curlResponse);
    }

    /**
     * Check Is Valid Respose From Telegram API
     *
     * @param string|null $apiResponseJson Respose From API
     */
    private function _validateApiResponse(
        ?string $apiResponseJson = null
    ): void {
        if (empty($apiResponseJson)) {
            throw new \Exception('Invalid Telegram API Response');
        }

        $apiResponse = (array) json_decode($apiResponseJson, true);

        if (!array_key_exists('ok', $apiResponse)) {
            throw new \Exception(sprintf(
                'Telegram API Error. Response: %s',
                $apiResponseJson
            ));
        }

        $apiResponse['ok'] = (bool) $apiResponse['ok'];

        if (!$apiResponse['ok']) {
            throw new \Exception(sprintf(
                'Telegram API Error. Response: %s',
                $apiResponseJson
            ));
        }
    }

    /**
     * Get  Curl Headers
     *
     * @param string $url URL Value
     *
     * @return array List Of Curl Headers
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
     * Get API URL
     *
     * @return string API URL
     */
    private function _getApiUrl(): string
    {
        $token = null;

        if (array_key_exists('token', $this->_credentials)) {
            $token = $this->_credentials['token'];
        }

        return sprintf(
            '%s/bot%s/sendMessage',
            static::TELEGRAM_API_URL,
            (string) $token
        );
    }

    /**
     * Check Is Valid Telegram API Credentials
     */
    private function _checkCredentials(): void
    {
        if (empty($this->_credentials)) {
            throw new \Exception('Credentials Are Not Set');
        }

        if (
            !array_key_exists('token', $this->_credentials) ||
            !array_key_exists('chats', $this->_credentials) ||
            !is_array($this->_credentials['chats'])
        ) {
            throw new \Exception('Credentials Has Bad Format');
        }

        if (strlen($this->_credentials['token']) < 1) {
            throw new \Exception('Token Is Not Set');
        }

        if (empty($this->_credentials['chats'])) {
            throw new \Exception('Chat List Is Not Set');
        }
    }
}
