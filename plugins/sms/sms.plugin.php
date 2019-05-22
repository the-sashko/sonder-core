<?php
class SmsPlugin
{
    const SMS_TEXT_MAX_LENGTH = 70;

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
            throw new Exception('Invalid SMS Provider');
        }

        if (
            !file_exists($responseProviderFile) ||
            !is_file($responseProviderFile)
        ) {
            throw new Exception('Invalid SMS Provider');
        }

        if (!file_exists($providerFile) || !is_file($providerFile)) {
            throw new Exception('Invalid SMS Provider');
        }

        include_once $credentialsProviderFile;
        include_once $responseProviderFile;
        include_once $providerFile;

        if (!class_exists($credentialsProviderClass)) {
            throw new Exception('Invalid SMS Provider');
        }

        if (!class_exists($responseProviderClass)) {
            throw new Exception('Invalid SMS Provider');
        }

        if (!class_exists($providerClass)) {
            throw new Exception('Invalid SMS Provider');
        }

        $this->_provider = new $providerClass();
    }

    public function sendMessage(
        string $phone   = '',
        string $message = ''
    ) : array
    {
        if ($this->_provider === NULL) {
            throw new Exception('SMS Provider Is Not Set');
        }

        if (!$this->_validatePhone($phone)) {
            return [FALSE, 'Invalid Phone Number'];
        }

        if (strlen($message) < 1) {
        }

        if (strlen($message) > static::SMS_TEXT_MAX_LENGTH) {
            return [FALSE, 'SMS Text Is Too Long'];
        }

        $response = $this->_provider->sendMessage($phone, $message);

        if (!$response->getStatus()) {
            return [FALSE, $response->getErrorMessage()];
        }

        return [TRUE, $response->getRemoteMessageCode()];
    }

    private function _validatePhone(string $phone = '') : bool
    {
        return preg_match('/^\+([0-9]+)$/su', $phone);
    }
}
?>