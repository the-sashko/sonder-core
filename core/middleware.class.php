<?php

namespace Sonder\Core;

use Sonder\Core\Interfaces\IMiddleware;

class CoreMiddleware extends CoreObject implements IMiddleware
{
    /**
     * @var RequestObject
     */
    private RequestObject $_request;

    public function __construct(RequestObject $request)
    {
        $this->_request = $request;
    }

    final public function getRequest(): RequestObject
    {
        return $this->_request;
    }
}
