<?php

namespace Sonder\Middlewares;

use Sonder\Core\CoreMiddleware;
use Sonder\Interfaces\IMiddleware;
use Sonder\Interfaces\ISessionMiddleware;

#[IMiddleware]
#[ISessionMiddleware]
final class SessionMiddleware
    extends CoreMiddleware
    implements ISessionMiddleware
{
    /**
     * @return void
     */
    final public function run(): void
    {
        $this->request->setSession();
    }
}
