<?php
class PushPlugin
{
    const PROVIDERS_DIR_PATH = __DIR__.'/providers';

    private $_provider = null;

    public function setProvider(?string $providerIdent = null): void
    {
        if (empty($providerIdent)) {
            throw new \Exception('Push Provider Is Not Set');
        }

        $this->_includeProviderCredentials($providerIdent);
        $this->_includeProviderResponce($providerIdent);

        $this->_provider = $this->_getProviderInstance($providerIdent);
    }

    public function getHtmlSnippet(): string
    {
        if (empty($this->_provider)) {
            throw new \Exception('Push Provider Is Not Set');
        }

        return $this->_provider->getHtmlSnippet();
    }

    public function sendMessage(
        ?string $message = null,
        ?string $title   = null,
        ?string $image   = null,
        ?string $url     = null
    ): IPushResponse
    {
        if (empty($this->_provider)) {
            throw new \Exception('Push Provider Is Not Set');
        }

        return $this->_provider->sendMessage($message, $title, $image, $url);
    }

    private function _includeProviderCredentials(string $providerIdent): void
    {
        $providerCredentialsClass = sprintf('%sCredentials', $providerIdent);

        $providerCredentialsFilePath = sprintf(
            '%s/%s/%s.php',
            static::PROVIDERS_DIR_PATH,
            $providerIdent,
            $providerCredentialsClass
        );

        if (
            !file_exists($providerCredentialsFilePath) ||
            !is_file($providerCredentialsFilePath)
        ) {
            throw new \Exception('Invalid Push Provider');
        }

        include_once $providerCredentialsFilePath;

        if (!class_exists($providerCredentialsClass)) {
            throw new \Exception('Invalid Push Provider');
        }
    }

    private function _includeProviderResponce(string $providerIdent): void
    {
        $providerResponseClass = sprintf('%sResponse', $providerIdent);

        $providerResponseFilePath = sprintf(
            '%s/%s/%s.php',
            static::PROVIDERS_DIR_PATH,
            $providerIdent,
            $providerResponseClass
        );

        if (
            !file_exists($providerResponseFilePath) ||
            !is_file($providerResponseFilePath)
        ) {
            throw new \Exception('Invalid Push Provider');
        }

        include_once $providerResponseFilePath;

        if (!class_exists($providerResponseClass)) {
            throw new \Exception('Invalid Push Provider');
        }
    }

    private function _getProviderInstance(string $providerIdent): IPushProvider
    {
        $providerClass = sprintf('%sProvider', $providerIdent);

        $providerFilePath = sprintf(
            '/providers/%s/%s.php',
            static::PROVIDERS_DIR_PATH,
            $providerIdent,
            $providerClass
        );

        if (!file_exists($providerFilePath) || !is_file($providerFilePath)) {
            throw new \Exception('Invalid Push Provider');
        }

        include_once $providerFilePath;

        if (!class_exists($providerClass)) {
            throw new \Exception('Invalid Push Provider');
        }

        return new $providerClass();
    }
}
