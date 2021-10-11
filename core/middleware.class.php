<?php

namespace SonderCore\Core;

use SonderCore\Core\RequestObject;

use SonderCore\Core\Interfaces\IMiddleware;

class CoreMiddleware implements IMiddleware
{
    /**
     * @var \SonderCore\Core\RequestObject
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
