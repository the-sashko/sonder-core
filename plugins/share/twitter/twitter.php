<?php
/**
 * Plugin For Sending Messages To Twitter
 */
class TwitterPlugin
{
    /**
     * @var int Twitter Max Length Of Message
     */
    const TWITTER_MESSAGE_MAX_LENGTH = 140;

    /**
     * @var object Instance Of Codebird
     */
    public $codebird = NULL;

    /**
     * @var array Credentials
     */
    public $credentials = [];

    /**
     * Set Twitter API Credentials
     *
     * @param array $credentials Twitter API Credentials
     */
    public function setCredentials(array $credentials = []) : void
    {
        if (!$this->_validateCredentials($credentials)) {
            throw new Exception('Twitter Credentials Has Bad Format');
        }

        $this->credentials = $credentials;
    }

    /**
     * Sending Message To Twitter
     *
     * @param string $message Message Text
     */
    public function send(string $message = '') : void
    {
        $this->_validateMessage($message);

        if (NULL === $this->codebird) {
            $this->_setCodebirdInstance();
        }

        $status = 'status='.$message;
        $res    = (array) $this->codebird->statuses_update($status);

        $this->_validateResponse($res);
    }

    /**
     * Set Codebird Instance
     */
    private function _setCodebirdInstance() : void
    {
        $consumerKey    = $this->credentials['consumer']['key'];
        $consumerSecret = $this->credentials['consumer']['secret'];
        $accessToken    = $this->credentials['access']['token'];
        $accessSecret   = $this->credentials['access']['secret'];

        \Codebird\Codebird::setConsumerKey($consumerKey, $consumerSecret);

        $this->codebird = \Codebird\Codebird::getInstance();
        $this->codebird->setToken($accessToken, $accessSecret);
    }

    /**
     * Check Is Message Has Valid Format
     *
     * @param string $message Message Text
     */
    private function _validateMessage(string $message = '') : void
    {
        if (strlen($message) > static::TWITTER_MESSAGE_MAX_LENGTH) {
            throw new Exception('Twitter API Error: Message Too Long');
        }

        if (!strlen(trim($message)) > 0) {
            throw new Exception('Twitter API Error: Message Empty');
        }
    }

    /**
     * Validate Twitter API Credentials
     *
     * @param array $credentials Twitter API Credentials
     *
     * @return bool Is Twitter API Credentials Data Has Valid Format
     */
    private function _validateCredentials(array $credentials = []) : bool
    {
        if (!array_key_exists('consumer', $credentials)) {
            return FALSE;
        }

        if (!is_array($credentials['consumer'])) {
            return FALSE;
        }

        if (!array_key_exists('key', $credentials['consumer'])) {
            return FALSE;
        }

        if (!array_key_exists('secret', $credentials['consumer'])) {
            return FALSE;
        }

        if (!array_key_exists('access', $credentials)) {
            return FALSE;
        }

        if (!is_array($credentials['access'])) {
            return FALSE;
        }

        if (!array_key_exists('token', $credentials['access'])) {
            return FALSE;
        }

        if (!array_key_exists('secret', $credentials['access'])) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Check Is Valid Respose From Twitter API
     *
     * @param array $res Respose From Twitter API
     *
     * @return bool Is Twitter API Respose Has Valid Format
     */
    private function _validateResponse(array $res = []) : bool
    {
        if (!count($res) > 0) {
            throw new Exception('Twitter Response Has Bad Format');
        }

        if (!array_key_exists('errors', $res)) {
            return TRUE;
        }

        $message = 'Unknow Twitter API Errror';

        $error = (array) $res['errors'];

        if (!count($error) > 0) {
            throw new Exception($error);
        }

        $error = (array) $error[0];

        if (!array_key_exists('message', $error)) {
            throw new Exception($error);
        }

        $error['message'] = (string) $error['message'];

        if (strlen($error['message']) > 0) {
            throw new Exception('Twitter API Error: '.$error['message']);
        }

        throw new Exception($error);
    }
}
?>
