<?php
class SmsPlugin
{
    const SMS_TEXT_MAX_LENGTH = 70;

    const PROVIDERS_DIR_PATH = __DIR__.'/providers';

    private $_provider = NULL;

    public function setProvider(?string $providerIdent = null): void
    {
        if (empty($providerIdent)) {
            throw new \Exception('SMS Plugin Provider Is Not Set');
        }

        $credentialsProviderClass = sprintf('%sCredentials', $providerIdent);

        $credentialsProviderFile = sprintf(
            '%s/%s/%s.php',
            static::PROVIDERS_DIR_PATH,
            $providerIdent,
            $credentialsProviderClass
        );

        $responseProviderClass = sprintf('%sResponse', $providerIdent);

        $responseProviderFile = sprintf(
            '%s/%s/%s.php',
            static::PROVIDERS_DIR_PATH,
            $providerIdent,
            $responseProviderClass
        );
        
        $providerClass = sprintf('%sProvider', $providerIdent);

        $providerFile = sprintf(
            '%s/%s/%s.php',
            static::PROVIDERS_DIR_PATH,
            $providerIdent,
            $providerClass
        );

        if (
            !file_exists($credentialsProviderFile) ||
            !is_file($credentialsProviderFile)
        ) {
            throw new \Exception('Invalid SMS Plugin Provider');
        }

        if (
            !file_exists($responseProviderFile) ||
            !is_file($responseProviderFile)
        ) {
            throw new \Exception('Invalid SMS Plugin Provider');
        }

        if (!file_exists($providerFile) || !is_file($providerFile)) {
            throw new \Exception('Invalid SMS Plugin Provider');
        }

        include_once $credentialsProviderFile;
        include_once $responseProviderFile;
        include_once $providerFile;

        if (!class_exists($credentialsProviderClass)) {
            throw new \Exception('Invalid SMS Plugin Provider');
        }

        if (!class_exists($responseProviderClass)) {
            throw new \Exception('Invalid SMS Plugin Provider');
        }

        if (!class_exists($providerClass)) {
            throw new \Exception('Invalid SMS Plugin Provider');
        }

        $this->_provider = new $providerClass();
    }

    public function sendMessage(
        ?string $phone   = null,
        ?string $message = null
    ): array
    {
        if (empty($phone)) {
            throw new \Exception('SMS Phone Is Not Set');
        }

        if (empty($message)) {
            throw new \Exception('SMS Message Is Not Set');
        }

        if (empty($this->_provider)) {
            throw new \Exception('SMS Provider Is Not Set');
        }

        if (!$this->_validatePhone($phone)) {
            return [false, 'Invalid Phone Number'];
        }

        if (strlen($message) > static::SMS_TEXT_MAX_LENGTH) {
            return [false, 'SMS Text Is Too Long'];
        }

        $response = $this->_provider->sendMessage($phone, $message);

        if (!$response->getStatus()) {
            return [false, $response->getErrorMessage()];
        }

        return [true, $response->getRemoteMessageCode()];
    }

    private function _validatePhone(?string $phone = null): bool
    {
        if (empty($phone)) {
            return false;
        }

        return preg_match('/^\+([0-9]+)$/su', $phone);
    }
}
