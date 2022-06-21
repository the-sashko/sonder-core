<?php

namespace Sonder\Interfaces;

use Attribute;

#[IController]
#[Attribute(Attribute::TARGET_CLASS)]
interface IApiController extends IController
{
    /**
     * @return IResponseObject
     */
    public function displayRun(): IResponseObject;
}
