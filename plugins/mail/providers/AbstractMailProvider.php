<?php
abstract class AbstractMailProvider implements IMailProvider
{
    public $credentials = NULL;

    public $response    = NULL;

    public function __construct(?string $providerIdent = NULL)
    {
        if (!empty($providerIdent)) {
            $this->_setCredentialsClass($providerIdent);
            $this->_setResponseClass($providerIdent);
        }
    }

    private function _setCredentialsClass(
        ?string $credentialsIdent = NULL
    ) : void
    {
        if (empty($credentialsIdent)) {
            throw new Exception('Invalid Mail Credentials Ident');
        }

        $credentialsClass = $credentialsIdent.'Credentials';

        $this->credentials = new $credentialsClass();
    }

    private function _setResponseClass(?string $responseIdent = NULL) : void
    {
        if (empty($responseIdent)) {
            throw new Exception('Invalid Mail Response Ident');
        }

        $responseClass = $responseIdent.'Response';

        $this->response = new $responseClass();
    }
}
?>
