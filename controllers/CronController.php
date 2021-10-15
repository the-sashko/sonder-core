<?php

namespace Sonder\Controllers;

use Sonder\Core\CoreController;
use Sonder\Core\Interfaces\IController;
use Sonder\Core\ResponseObject;

final class CronController extends CoreController implements IController
{
    /**
     * @return ResponseObject
     */
    public function displayRun(): ResponseObject
    {
        //TODO

        return $this->render();
    }
}
