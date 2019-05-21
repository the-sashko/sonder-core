<?php
class SmsPlugin
{
    const SMS_TEXT_MAX_LENGTH = 70;

    private $_provider = NULL;

    public function setProvider(string $providerIdent = '') : void
    {
        $providerClass = $providerIdent.'Provider';
        $providerFile  = __DIR__.'/providers/'.$providerIdent.'/'
                         .$providerClass.'.php';

        if (file_exists($providerFile) && is_file($providerFile)) {
            throw new Exception('Invalid SMS Provider');
        }

        include_once $providerFile;

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
            throw new Exception('Invalid Phone Number');
        }

        if (strlen($message) < 1) {
            throw new Exception('SMS Text Is Empty');
        }

        if (strlen($message) > static::SMS_TEXT_MAX_LENGTH) {
            throw new Exception('SMS Text Is Too Long');
        }

        $response = $this->_provider->sendMessage($phone, $message);

        if (!$response->getStatus()) {
            //....

            return [false, $err];
        }
        
        return [true, ''];
    }
}
?>