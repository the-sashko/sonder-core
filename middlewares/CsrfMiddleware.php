<?php

namespace Sonder\Middlewares;

use Sonder\Core\CoreMiddleware;
use Sonder\Enums\ConfigNamesEnum;
use Sonder\Exceptions\ConfigException;
use Sonder\Exceptions\CoreException;
use Sonder\Interfaces\ICsrfMiddleware;
use Sonder\Interfaces\IMiddleware;
use Sonder\Exceptions\AppException;
use Sonder\Exceptions\MiddlewareException;

#[IMiddleware]
#[ICsrfMiddleware]
final class CsrfMiddleware extends CoreMiddleware implements ICsrfMiddleware
{
    private const CSRF_TOKEN_NAME = 'csrf_token';

    private const SALT_CONFIG_VALUE = 'salt';

    /**
     * @return void
     * @throws ConfigException
     * @throws CoreException
     * @throws MiddlewareException
     */
    final public function run(): void
    {
        if (empty($this->request->getSession())) {
            throw new MiddlewareException(
                MiddlewareException::MESSAGE_MIDDLEWARE_CSRF_RUNNING_BEFORE_SESSION,
                AppException::CODE_MIDDLEWARE_CSRF_RUNNING_BEFORE_SESSION
            );
        }

        $cryptPlugin = $this->getPlugin('crypt');

        $ip = $this->request->getIp();
        $userAgent = $this->request->getUserAgent();
        $url = $this->request->getFullUrl();

        $uniqueUserString = sprintf('%s+%s+%s', $url, $ip, $userAgent);

        $salt = $this->config->getValue(
            ConfigNamesEnum::CRYPT,
            CsrfMiddleware::SALT_CONFIG_VALUE
        );

        $csrfTokenFromSession = (string)$this->request->getSession()->get(
            CsrfMiddleware::CSRF_TOKEN_NAME
        );

        $csrfTokenFromRequest = (string)$this->request->getPostValue(
            CsrfMiddleware::CSRF_TOKEN_NAME
        );

        $postValues = $this->request->getPostValues();

        if (
            $this->request->getHttpMethod()->isPost()
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

            $_FILES = [];
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
