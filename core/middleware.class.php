<?php

namespace Sonder\Core;

class CoreMiddleware extends CoreObject
{
    /**
     * @var RequestObject
     */
    protected RequestObject $request;

    public function __construct(RequestObject &$request)
    {
        parent::__construct();

        $this->request = $request;
    }
}
