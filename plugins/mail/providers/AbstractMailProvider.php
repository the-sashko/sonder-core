<?php
abstract class AbstractMailProvider implements IMailProvider
{
    public $credentials = null;

    public $response = null;

    public function __construct(?string $providerIdent = null)
    {
        if (!empty($providerIdent)) {
            $this->_setCredentialsClass($providerIdent);
            $this->_setResponseClass($providerIdent);
        }
    }

    private function _setCredentialsClass(
        ?string $credentialsIdent = null
    ): void
    {
        if (empty($credentialsIdent)) {
            throw new \Exception('Invalid Mail Credentials Ident');
        }

        $credentialsClass  = sprintf('%sCredentials', $credentialsIdent);
        $this->credentials = new $credentialsClass();
    }

    private function _setResponseClass(?string $responseIdent = null): void
    {
        if (empty($responseIdent)) {
            throw new \Exception('Invalid Mail Response Ident');
        }

        $responseClass  = sprintf('%sResponse', $responseIdent);
        $this->response = new $responseClass();
    }
}
