<?php

namespace Sonder\Middlewares;

use Exception;
use Sonder\Core\CoreMiddleware;
use Sonder\Core\Interfaces\IMiddleware;
use Sonder\Exceptions\AppException;
use Sonder\Exceptions\MiddlewareException;

final class UserMiddleware extends CoreMiddleware implements IMiddleware
{
    /**
     * @throws Exception
     */
    final public function run(): void
    {
        if (empty($this->request->getSession())) {
            if (empty($this->request->getSession())) {
                throw new MiddlewareException(
                    MiddlewareException::MESSAGE_MIDDLEWARE_USER_RUNNING_BEFORE_SESSION,
                    AppException::CODE_MIDDLEWARE_USER_RUNNING_BEFORE_SESSION
                );
            }
        }

        $this->request->setUser($this->getModel('user'));
    }
}
