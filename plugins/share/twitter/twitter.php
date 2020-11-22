<?php
/**
 * Plugin For Sending Messages To Twitter
 */
class TwitterPlugin
{
    /**
     * @var int Twitter Max Length Of Message
     */
    const MESSAGE_MAX_LENGTH = 140;

    /**
     * @var object|null Instance Of Codebird
     */
    private $_codebird = null;

    /**
     * @var array|null Credentials
     */
    private $_credentials = null;

    /**
     * Set Twitter API Credentials
     *
     * @param array|null $credentials Twitter API Credentials
     */
    public function setCredentials(?array $credentials = null): void
    {
        $this->_credentials = $credentials;
    }

    /**
     * Sending Message To Twitter
     *
     * @param string|null $message Message Text
     */
    public function send(?string $message = null): void
    {
        $this->_validateMessage($message);

        if (null === $this->_codebird) {
            $this->_setCodebirdInstance();
        }

        $status   = sprintf('status=%s', $message);
        $response = (array) $this->_codebird->statuses_update($status);

        $this->_validateResponse($response);
    }

    /**
     * Set Codebird Instance
     */
    private function _setCodebirdInstance(): void
    {
        $consumerKey    = $this->_getConsumerKey();
        $consumerSecret = $this->_getConsumerSecret();
        $accessToken    = $this->_getAccessToken();
        $accessSecret   = $this->_getAccessSecret();

        \Codebird\Codebird::setConsumerKey($consumerKey, $consumerSecret);

        $this->_codebird = \Codebird\Codebird::getInstance();

        $this->_codebird->setToken($accessToken, $accessSecret);
    }

    /**
     * Check Credentials Format
     *
     * @return string Consumer Key
     */
    private function _getConsumerKey(): string
    {
        if (
            empty($this->_credentials) ||
            !array_key_exists('consumer', $this->_credentials) ||
            !is_array($this->_credentials['consumer']) ||
            !array_key_exists('key', $this->_credentials['consumer']) ||
            empty($this->_credentials['consumer']['key'])
        ) {
            throw new \Exception('Twitter Config Has Bad Format');
        }

        return (string) $this->_credentials['consumer']['key'];
    }

    /**
     * Check Credentials Format
     *
     * @return string Consumer Secret
     */
    private function _getConsumerSecret(): string
    {
        if (
            empty($this->_credentials) ||
            !array_key_exists('consumer', $this->_credentials) ||
            !is_array($this->_credentials['consumer']) ||
            !array_key_exists('secret', $this->_credentials['consumer']) ||
            empty($this->_credentials['consumer']['secret'])
        ) {
            throw new \Exception('Twitter Config Has Bad Format');
        }

        return (string) $this->_credentials['consumer']['secret'];
    }

    /**
     * Check Credentials Format
     *
     * @return string Access Token
     */
    private function _getAccessToken(): string
    {
        if (
            empty($this->_credentials) ||
            !array_key_exists('access', $this->_credentials) ||
            !is_array($this->_credentials['access']) ||
            !array_key_exists('token', $this->_credentials['access']) ||
            empty($this->_credentials['access']['token'])
        ) {
            throw new \Exception('Twitter Config Has Bad Format');
        }

        return (string) $this->_credentials['access']['token'];
    }

    /**
     * Check Credentials Format
     *
     * @return string Access Secret
     */
    private function _getAccessSecret(): string
    {
        if (
            empty($this->_credentials) ||
            !array_key_exists('access', $this->_credentials) ||
            !is_array($this->_credentials['access']) ||
            !array_key_exists('secret', $this->_credentials['access']) ||
            empty($this->_credentials['access']['secret'])
        ) {
            throw new \Exception('Twitter Config Has Bad Format');
        }

        return (string) $this->_credentials['access']['secret'];
    }

    /**
     * Check Is Message Has Valid Format
     *
     * @param string|null $message Message Text
     */
    private function _validateMessage(?string $message = null): void
    {
        if (empty($message)) {
            throw new Exception('Twitter Message Is Not Set');
        }

        if (strlen($message) > static::MESSAGE_MAX_LENGTH) {
            throw new Exception('Twitter API Error: Message Too Long');
        }

        if (empty(trim($message))) {
            throw new Exception('Twitter API Error: Message Empty');
        }
    }

    /**
     * Check Is Valid Respose From Twitter API
     *
     * @param array|null $response Respose From Twitter API
     *
     * @return bool Is Twitter API Respose Has Valid Format
     */
    private function _validateResponse(?array $response = null): bool
    {
        if (empty($response)) {
            throw new \Exception('Twitter Response Has Bad Format');
        }

        if (!array_key_exists('errors', $response)) {
            return true;
        }

        $errors = (array) $response['errors'];

        if (empty($errors)) {
            throw new \Exception('Unknow Twitter API Errror');
        }

        $error = (array) array_shift($errors);

        if (!array_key_exists('message', $error)) {
            throw new \Exception('Unknow Twitter API Errror');
        }

        $error['message'] = (string) $error['message'];

        if (empty($error['message'])) {
            throw new \Exception('Unknow Twitter API Errror');
        }

        throw new \Exception($error['message']);
    }
}
