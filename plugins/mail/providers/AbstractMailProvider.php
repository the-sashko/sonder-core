<?php

namespace Sonder\Plugins\Mail\Providers;

use Exception;
use Sonder\Plugins\Mail\Interfaces\IMailCredentials;
use Sonder\Plugins\Mail\Interfaces\IMailProvider;
use Sonder\Plugins\Mail\Interfaces\IMailResponse;

abstract class AbstractMailProvider implements IMailProvider
{
    /**
     * @var IMailCredentials
     */
    protected IMailCredentials $credentials;

    /**
     * @var IMailResponse
     */
    protected IMailResponse $response;

    /**
     * @param string|null $providerIdent
     *
     * @throws Exception
     */
    public function __construct(?string $providerIdent = null)
    {
        if (!empty($providerIdent)) {
            $this->_setCredentialsClass($providerIdent);
            $this->_setResponseClass($providerIdent);
        }
    }

    /**
     * @param string|null $credentialsIdent
     *
     * @throws Exception
     */
    private function _setCredentialsClass(
        ?string $credentialsIdent = null
    ): void
    {
        if (empty($credentialsIdent)) {
            throw new Exception('Invalid Mail Credentials Ident');
        }

        $credentialsClass = sprintf('%sCredentials', $credentialsIdent);
        $this->credentials = new $credentialsClass();
    }

    /**
     * @param string|null $responseIdent
     *
     * @throws Exception
     */
    private function _setResponseClass(?string $responseIdent = null): void
    {
        if (empty($responseIdent)) {
            throw new Exception('Invalid Mail Response Ident');
        }

        $responseClass = sprintf('%sResponse', $responseIdent);
        $this->response = new $responseClass();
    }
}
