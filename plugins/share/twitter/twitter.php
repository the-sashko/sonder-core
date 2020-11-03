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
        if (!$this->_validateCredentials($credentials)) {
            throw new Exception('Twitter Credentials Has Bad Format');
        }

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
        $consumerCredentials = null;
        $accessCredentials   = null;

        $consumerKey    = null;
        $consumerSecret = null;
        $accessToken    = null;
        $accessSecret   = null;

        if (
            !empty($this->_credentials) &&
            array_key_exists('consumer', $this->_credentials)
        ) {
            $consumerCredentials = $this->_credentials['consumer'];
        }

        if (
            !empty($this->_credentials) &&
            array_key_exists('access', $this->_credentials)
        ) {
            $accessCredentials = $this->_credentials['access'];
        }

        if (
            !empty($consumerCredentials) &&
            array_key_exists('key', $consumerCredentials)
        ) {
            $consumerKey = $consumerCredentials['key'];
        }

        if (
            !empty($consumerCredentials) &&
            array_key_exists('secret', $consumerCredentials)
        ) {
            $consumerSecret = $consumerCredentials['secret'];
        }

        if (
            !empty($accessCredentials) &&
            array_key_exists('token', $accessCredentials)
        ) {
            $accessToken = $accessCredentials['token'];
        }

        if (
            !empty($accessCredentials) &&
            array_key_exists('secret', $accessCredentials)
        ) {
            $accessSecret = $accessCredentials['secret'];
        }

        if (!empty($consumerKey) && !empty($consumerSecret)) {
            \Codebird\Codebird::setConsumerKey($consumerKey, $consumerSecret);
        }

        $this->_codebird = \Codebird\Codebird::getInstance();

        if (!empty($accessToken) && !empty($accessSecret)) {
            $this->_codebird->setToken($accessToken, $accessSecret);
        }
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
     * Validate Twitter API Credentials
     *
     * @param array|null $credentials Twitter API Credentials
     *
     * @return bool Is Twitter API Credentials Data Has Valid Format
     */
    private function _validateCredentials(?array $credentials = null): bool
    {
        if (empty($credentials)) {
            throw new Exception('Twitter Credentials Is Not Set');
        }

        if (!array_key_exists('consumer', $credentials)) {
            return false;
        }

        if (!is_array($credentials['consumer'])) {
            return false;
        }

        if (!array_key_exists('key', $credentials['consumer'])) {
            return false;
        }

        if (!array_key_exists('secret', $credentials['consumer'])) {
            return false;
        }

        if (!array_key_exists('access', $credentials)) {
            return false;
        }

        if (!is_array($credentials['access'])) {
            return false;
        }

        if (!array_key_exists('token', $credentials['access'])) {
            return false;
        }

        if (!array_key_exists('secret', $credentials['access'])) {
            return false;
        }

        return true;
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
