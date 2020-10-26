<?php
class PushPlugin
{
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
            throw new Exception('Invalid Push Provider');
        }

        if (
            !file_exists($responseProviderFile) ||
            !is_file($responseProviderFile)
        ) {
            throw new Exception('Invalid Push Provider');
        }

        if (!file_exists($providerFile) || !is_file($providerFile)) {
            throw new Exception('Invalid Push Provider');
        }

        include_once $credentialsProviderFile;
        include_once $responseProviderFile;
        include_once $providerFile;

        if (!class_exists($credentialsProviderClass)) {
            throw new Exception('Invalid Push Provider');
        }

        if (!class_exists($responseProviderClass)) {
            throw new Exception('Invalid Push Provider');
        }

        if (!class_exists($providerClass)) {
            throw new Exception('Invalid Push Provider');
        }

        $this->_provider = new $providerClass();
    }

    public function getHTMLSnippet() : string
    {
        if ($this->_provider === NULL) {
            throw new Exception('Push Provider Is Not Set');
        }

        return $this->_provider->getHTMLSnippet();
    }

    public function sendMessage(
        string $message = '',
        string $title   = '',
        string $image   = '',
        string $url     = '#'
    ) : IPushResponse
    {
        if ($this->_provider === NULL) {
            throw new Exception('Push Provider Is Not Set');
        }

        return $this->_provider->sendMessage($message, $title, $image, $url);
    }
}
?>
