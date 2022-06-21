<?php

namespace Sonder\Middlewares;

use Sonder\Core\CoreMiddleware;
use Sonder\Exceptions\CoreException;
use Sonder\Interfaces\IMiddleware;
use Sonder\Exceptions\AppException;
use Sonder\Exceptions\MiddlewareException;
use Sonder\Interfaces\IUserModel;
use Sonder\Interfaces\IUserMiddleware;

#[IMiddleware]
#[IUserMiddleware]
final class UserMiddleware extends CoreMiddleware implements IUserMiddleware
{
    /**
     * @return void
     * @throws MiddlewareException
     * @throws CoreException
     */
    final public function run(): void
    {
        if (empty($this->request->getSession())) {
            throw new MiddlewareException(
                MiddlewareException::MESSAGE_MIDDLEWARE_USER_RUNNING_BEFORE_SESSION,
                AppException::CODE_MIDDLEWARE_USER_RUNNING_BEFORE_SESSION
            );
        }

        /* @var IUserModel $userModel */
        $userModel = $this->getModel('user');

        $this->request->setUser($userModel);
    }
}
