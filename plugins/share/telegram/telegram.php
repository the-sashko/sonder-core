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
     * @var array Telegram API Credentials
     */
    public $credentials = [];

    /**
     * Set Telegram API Credentials
     *
     * @param array $credentials Telegram API Credentials
     */
    public function setCredentials(array $credentials = []) : void
    {
        $this->credentials = $credentials;
    }

    /**
     * Sending Message To Telegram Usera, Chats Or Channel
     *
     * @param string $message Message Text Value
     */
    public function send(string $message = '') : void
    {
        $this->_checkCredentials();

        $message = $this->_getFomatedMessage($message);

        if (strlen($message) < 3) {
            throw new Exception('Message To Short');
        }

        foreach ($this->credentials['chats'] as $chat) {
            $this->_sendToChat($message, $chat);
        }
    }

    /**
     * Removing From Message Text Tags And Extra Spaces
     *
     * @param string $message Input Message Text Value
     *
     * @return string Output Message Text Value
     */
    private function _getFomatedMessage(string $message = '') : string
    {
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
     * @param string $message Message
     * @param string $chatID ID Of Chat Or Channel
     */
    private function _sendToChat(
        string $message = '',
        string $chatID = ''
    ) : void
    {
        $url = $this->_getAPIURL();
        $url = $url.'?chat_id='.$chatID;
        $url = $url.'&text='.$message;

        $this->_sendToRemoteAPI($url);
    }

    /**
     * Send Request To API URL
     *
     * @param string $url API URL
     */
    private function _sendToRemoteAPI(string $url = '') : void
    {
        $curl = curl_init();

        $curlHeaders = $this->_getCurlHeaders($url);
        curl_setopt_array($curl, $curlHeaders);

        $curlResponse = curl_exec($curl);
        $curlError    = curl_error($curl);

        curl_close($curl);

        if ($curlError) {
            throw new Exception('Curl Error: '.$curlError);
        }

        $this->_validateAPIResponse($curlResponse);
    }

    /**
     * Check Is Valid Respose From Telegram API
     *
     * @param string $curlResponseJSON Respose From API
     */
    private function _validateAPIResponse(string $curlResponseJSON = '') : void
    {
        if (strlen($curlResponseJSON) < 1) {
            throw new Exception('Invalid Telegram API Response');
        }

        $curlResponse = (array) json_decode($curlResponseJSON, true);

        if (!array_key_exists('ok', $curlResponse)) {
            throw new Exception('Telegram API Error: '.$curlResponseJSON);
        }

        $curlResponse['ok'] = (bool) $curlResponse['ok'];

        if (!$curlResponse['ok']) {
            throw new Exception('Telegram API Error: '.$curlResponseJSON);
        }
    }

    /**
     * Get  Curl Headers
     *
     * @param string $url URL Value
     *
     * @return array List Of Curl Headers
     */
    private function _getCurlHeaders(string $url = '') : array
    {
        return [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_POSTFIELDS     => '',
            CURLOPT_FAILONERROR    => FALSE
        ];
    }

    /**
     * Get API URL
     *
     * @return string API URL
     */
    private function _getAPIURL() : string
    {
        $token = $this->credentials['token'];

        return static::TELEGRAM_API_URL.'/bot'.$token.'/sendMessage';
    }

    /**
     * Check Is Valid Telegram API Credentials
     */
    private function _checkCredentials() : void
    {
        if (count($this->credentials) < 1) {
            throw new Exception('Credentials Are Not Set');
        }

        if (
            !array_key_exists('token', $this->credentials) ||
            !array_key_exists('chats', $this->credentials) ||
            !is_array($this->credentials['chats'])
        ) {
            throw new Exception('Credentials Has Bad Format');
        }

        if (strlen($this->credentials['token']) < 1) {
            throw new Exception('Token Is Not Set');
        }

        if (count($this->credentials['chats']) < 1) {
            throw new Exception('Chat List Is Not Set');
        }
    }
}
?>
