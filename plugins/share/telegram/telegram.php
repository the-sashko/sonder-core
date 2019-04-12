<?php
/**
 * Plugin For Autoposting Links To Telegram
 */
class TelegramPlugin
{
    const TELEGRAM_API_URL = 'https://api.telegram.org';

    public $credentials = [];

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    public function setCredentials(array $credentials = []) : void
    {
        $this->credentials = $credentials;
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
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
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
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
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _sendToChat(
        string $message = '',
        string $chat = ''
    ) : void
    {
        $url = $this->_getAPIURL();
        $url = $url.'?chat_id='.$chat;
        $url = $url.'&text='.$message;

        $this->_sendToRemoteAPI($url);
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _sendToRemoteAPI(string $url = '') : void
    {
        $curl = curl_init();

        $curlHeaders = $this->_getCurlHeaders($url);
        curl_setopt_array($curl, $curlHeaders);

        $curlResponse = curl_exec($curl);
        $curlError = curl_error($curl);

        curl_close($curl);

        if ($curlError) {
            throw new Exception('Curl Error: '.$curlError);
        }

        $this->_validateAPIResponse($curlResponse);
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
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
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _getCurlHeaders(string $url = '') : array
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
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _getAPIURL() : string
    {
        return static::TELEGRAM_API_URL.'/bot'.
               $this->credentials['token'].'/sendMessage';
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
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