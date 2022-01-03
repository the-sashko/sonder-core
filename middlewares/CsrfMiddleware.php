<?php

namespace Sonder\Middlewares;

use Exception;
use Sonder\Core\CoreMiddleware;
use Sonder\Core\Interfaces\IMiddleware;
use Sonder\Core\RequestObject;

final class CsrfMiddleware extends CoreMiddleware implements IMiddleware
{
    const CSRF_TOKEN_NAME = 'csrf_token';

    /**
     * @throws Exception
     */
    final public function run(): void
    {
        if (empty($this->request->getSession())) {
            $errorMessage = 'CSRF Middleware Must Be Run After Session ' .
                'Middleware';

            throw new Exception($errorMessage);
        }

        $cryptPlugin = $this->getPlugin('crypt');

        $ip = $this->request->getIp();
        $userAgent = $this->request->getUserAgent();
        $url = $this->request->getFullUrl();

        $uniqueUserString = sprintf('%s+%s+%s', $url, $ip, $userAgent);
        $salt = $this->config->getValue('crypt', 'salt');

        $csrfTokenFromSession = (string)$this->request->getSession()->get(
            CsrfMiddleware::CSRF_TOKEN_NAME
        );

        $csrfTokenFromRequest = (string)$this->request->getPostValue(
            CsrfMiddleware::CSRF_TOKEN_NAME
        );

        $httpMethod = $this->request->getHttpMethod();

        $postValues = $this->request->getPostValues();

        if (
            $httpMethod == RequestObject::HTTP_METHOD_POST &&
            (
                empty($csrfTokenFromSession) ||
                $csrfTokenFromSession != $csrfTokenFromRequest
            )
        ) {
            $loggerPlugin = $this->getPlugin('logger');

            $errorMessage = 'Bad CSRF Token. CSRF Token From Session: "%s". ' .
                'CSRF Token From Request: "%s". URL: "%s". POST Values: "%s"';

            $errorMessage = sprintf(
                $errorMessage,
                $csrfTokenFromSession,
                $csrfTokenFromRequest,
                $this->request->getFullUrl(),
                json_encode($this->request->getPostValues())

            );

            $loggerPlugin->logError($errorMessage, 'security');

            $postValues = null;
        }

        if (
            !empty($postValues) &&
            array_key_exists(CsrfMiddleware::CSRF_TOKEN_NAME, $postValues)
        ) {
            unset($postValues[CsrfMiddleware::CSRF_TOKEN_NAME]);
        }

        $csrfToken = $cryptPlugin->getCsrfToken($uniqueUserString, $salt);

        $this->request->getSession()->set('csrf_token', $csrfToken);
        $this->request->setCsrfToken($csrfToken);
        $this->request->setPostValues($postValues);
    }
}
