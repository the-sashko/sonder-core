<?php

namespace Sonder\Core;

use Sonder\Core\Interfaces\IController;

class CoreController extends CoreObject implements IController
{
    protected RequestObject $request;

    private ResponseObject $_response;

    public function __construct(RequestObject $request)
    {
        parent::__construct();

        $this->request = $request;

        $this->_response = new ResponseObject();
    }

    final protected function redirect(
        string $url,
        bool   $isPermanent = false
    ): void
    {
        $this->_response->redirect->setUrl($url);
        $this->_response->redirect->setIsPermanent($isPermanent);
    }

    final protected function assign(?array $values = null): void
    {
        //TODO
    }

    final protected function render(?string $view = null): ResponseObject
    {
        $this->_response->setContent('test 1 2 3');

        return $this->_response;
    }
}
