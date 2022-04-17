<?php

namespace Sonder\Core;

use Exception;

class CoreMiddleware extends CoreObject
{
    /**
     * @var RequestObject
     */
    protected RequestObject $request;

    /**
     * @var ResponseObject|null
     */
    protected ?ResponseObject $response = null;

    /**
     * @param RequestObject $request
     * @throws Exception
     */
    public function __construct(RequestObject $request)
    {
        parent::__construct();

        $this->request = $request;
    }

    /**
     * @return ResponseObject|null
     */
    public function getResponse(): ?ResponseObject
    {
        return $this->response;
    }
}
