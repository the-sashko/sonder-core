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

        $this->_includeCredentialsProvider($providerIdent);
        $this->_includeResponseProvider($providerIdent);

        $providerClass = sprintf('%sProvider', $providerIdent);

        $providerFile = sprintf(
            '%s/%s/%s.php',
            static::PROVIDERS_DIR_PATH,
            $providerIdent,
            $providerClass
        );

        if (!file_exists($providerFile) || !is_file($providerFile)) {
            throw new \Exception('Invalid SMS Plugin Provider');
        }

        include_once $providerFile;

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

    public function _includeCredentialsProvider(string $providerIdent): void
    {
        $credentialsProviderClass = $this->_getCredentialsProviderClass(
            $providerIdent
        );

        $credentialsProviderFilePath = $this->_getCredentialsProviderFilePath(
            $providerIdent,
            $credentialsProviderClass
        );

        include_once $credentialsProviderFilePath;

        if (!class_exists($credentialsProviderClass)) {
            throw new \Exception('Invalid SMS Plugin Provider');
        }
    }

    public function _getCredentialsProviderClass(string $providerIdent): string
    {
        return sprintf('%sCredentials', $providerIdent);
    }

    public function _getCredentialsProviderFilePath(
        string $providerIdent,
        string $providerClass
    ): string
    {
        $credentialsProviderFilePath = sprintf(
            '%s/%s/%s.php',
            static::PROVIDERS_DIR_PATH,
            $providerIdent,
            $providerClass
        );

        if (
            !file_exists($credentialsProviderFilePath) ||
            !is_file($credentialsProviderFilePath)
        ) {
            throw new \Exception('Invalid SMS Plugin Provider');
        }

        return $credentialsProviderFilePath;
    }

    public function _includeResponseProvider(string $providerIdent): void
    {
        $responseProviderClass = $this->_getResponseProviderClass(
            $providerIdent
        );

        $responseProviderFilePath = $this->_getResponseProviderFilePath(
            $providerIdent,
            $responseProviderClass
        );

        include_once $responseProviderFilePath;

        if (!class_exists($responseProviderClass)) {
            throw new \Exception('Invalid SMS Plugin Provider');
        }
    }

    public function _getResponseProviderClass(string $providerIdent): string
    {
        return sprintf('%sResponse', $providerIdent);
    }

    public function _getResponseProviderFilePath(
        string $providerIdent,
        string $responseProviderClass
    ): string
    {
        $responseProviderFilePath = sprintf(
            '%s/%s/%s.php',
            static::PROVIDERS_DIR_PATH,
            $providerIdent,
            $responseProviderClass
        );

        if (
            !file_exists($responseProviderFilePath) ||
            !is_file($responseProviderFilePath)
        ) {
            throw new \Exception('Invalid SMS Plugin Provider');
        }

        return $responseProviderFilePath;
    }
}
