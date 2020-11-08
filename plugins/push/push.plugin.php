<?php
class PushPlugin
{
    private $_provider = null;

    public function setProvider(?string $providerIdent = null): void
    {
        if (empty($providerIdent)) {
            throw new \Exception('Push Provider Is Not Set');
        }

        $credentialsProviderClass = sprintf('%sCredentials', $providerIdent);

        $credentialsProviderFile = __DIR__.'/providers/%s/%s.php';

        $credentialsProviderFile = sprintf(
            $credentialsProviderFile,
            $providerIdent,
            $credentialsProviderClass
        );

        $responseProviderClass = sprintf('%sResponse', $providerIdent);

        $responseProviderFile = __DIR__.'/providers/%s/%s.php';

        $responseProviderFile = sprintf(
            $responseProviderFile,
            $providerIdent,
            $responseProviderClass
        );

        $providerClass = sprintf('%sProvider', $providerIdent);

        $providerFile = __DIR__.'/providers/%s/%s.php';
        $providerFile = sprintf($providerFile, $providerIdent, $providerClass);

        if (
            !file_exists($credentialsProviderFile) ||
            !is_file($credentialsProviderFile)
        ) {
            throw new \Exception('Invalid Push Provider');
        }

        if (
            !file_exists($responseProviderFile) ||
            !is_file($responseProviderFile)
        ) {
            throw new \Exception('Invalid Push Provider');
        }

        if (!file_exists($providerFile) || !is_file($providerFile)) {
            throw new \Exception('Invalid Push Provider');
        }

        include_once $credentialsProviderFile;
        include_once $responseProviderFile;
        include_once $providerFile;

        if (!class_exists($credentialsProviderClass)) {
            throw new \Exception('Invalid Push Provider');
        }

        if (!class_exists($responseProviderClass)) {
            throw new \Exception('Invalid Push Provider');
        }

        if (!class_exists($providerClass)) {
            throw new \Exception('Invalid Push Provider');
        }

        $this->_provider = new $providerClass();
    }

    public function getHTMLSnippet(): string
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
}
