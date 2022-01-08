<?php

namespace Sonder\Middlewares;

use Exception;
use Sonder\Core\CoreMiddleware;
use Sonder\Core\Interfaces\IMiddleware;

final class UserMiddleware extends CoreMiddleware implements IMiddleware
{
    /**
     * @throws Exception
     */
    final public function run(): void
    {
        if (empty($this->request->getSession())) {
            $errorMessage = 'User Middleware Must Be Run After Session ' .
                'Middleware';

            throw new Exception($errorMessage);
        }

        $this->request->setUser($this->getModel('user'));
    }
}
