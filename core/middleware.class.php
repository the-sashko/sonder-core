<?php

namespace Sonder\Core;

use Sonder\Core\Interfaces\ICoreMiddleware;
use Sonder\Interfaces\IRequestObject;
use Sonder\Interfaces\IResponseObject;

#[ICoreMiddleware]
class CoreMiddleware extends CoreObject implements ICoreMiddleware
{
    /**
     * @param IRequestObject $request
     * @param IResponseObject|null $response
     */
    public function __construct(
        #[IRequestObject]
        protected IRequestObject $request,
        #[IResponseObject]
        protected ?IResponseObject $response = null
    ) {
        parent::__construct();
    }

    /**
     * @return IResponseObject|null
     */
    public function getResponse(): ?IResponseObject
    {
        return $this->response;
    }
}
