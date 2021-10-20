<?php
namespace Sonder\Middlewares;

use Exception;
use Sonder\Core\CoreMiddleware;
use Sonder\Core\Interfaces\IMiddleware;

final class SessionMiddleware extends CoreMiddleware implements IMiddleware
{
    /**
     * @throws Exception
     */
    final public function run(): void
    {
        $this->request->setSession();
    }
}
