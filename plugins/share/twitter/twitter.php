<?php
class TwitterPlugin {
    const TWITTER_MESSAGE_MAX_LENGTH = 140;

    public $codebird = NULL;
    public $credentials = [];

    public function setCredentials(array $credentials = []) : void
    {
        if (!$this->_validateCredentials($credentials)) {
            throw new Exception('Twitter Credentials Has Bad Format');
        }

        $this->credentials = $credentials;
    }

    public function send(string $message = '') : void
    {
        $this->_validateMessage($message);

        if ($this->codebird == NULL) {
            $this->_setCodebirdInstance();
        }

        $status = 'status='.$message;
        $res = (array) $this->codebird->statuses_update($status);

        $this->_validateResponse($res);
    }

    private function _setCodebirdInstance() : void
    {
        $consumerKey = $this->credentials['consumer']['key'];
        $consumerSecret = $this->credentials['consumer']['secret'];
        $accessToken = $this->credentials['access']['token'];
        $accessSecret = $this->credentials['access']['secret'];

        \Codebird\Codebird::setConsumerKey($consumerKey, $consumerSecret);
        
        $this->codebird = \Codebird\Codebird::getInstance();
        $this->codebird->setToken($accessToken, $accessSecret);
    }

    private function _validateMessage(string $message = '') : void
    {
        if (strlen($message) > static::TWITTER_MESSAGE_MAX_LENGTH) {
            throw new Exception('Twitter API Error: Message Too Long');
        }


        if (!strlen(trim($message)) > 0) {
            throw new Exception('Twitter API Error: Message Empty');
        }
    }

    private function _validateCredentials(array $credentials = []) : bool
    {
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

    private function _validateResponse(array $res = []) : bool
    {
        if (!count($res) > 0) {
            throw new Exception('Twitter Response Has Bad Format');
        }

        if (!array_key_exists('errors', $res)) {
            return true;
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