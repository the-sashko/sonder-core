<?php

namespace Sonder\Controllers;

use Sonder\Core\CoreController;
use Sonder\Core\Interfaces\IController;
use Sonder\Core\ResponseObject;

final class ApiController extends CoreController implements IController
{
    final public function displayRun(): ResponseObject
    {
        $response = new ResponseObject();
        $response->setContent('{"foo":"bar"}');

        return $response;

        //TODO
    }
}
