<?php
class Test extends App
{
    public function __construct()
    {
        parent::__construct();
        session_start();
    }

    private function _redirect() : void
    {
        //Mock For App::_redirect()
    }

    private function _replaceURI() : void
    {
        //Mock For App::_replaceURI()
    }

    public function run() : void
    {
        //Mock For App::run()
        $this->_autoload();
    }

    private function _parseURI() : array
    {
        //Mock For App::_parseURI()

        return [];
    }

    private function _isControllerExist(string $controller = '') : bool
    {
        //Mock For App::_isControllerExist()

        return true;
    }

    private function _isValidControllerAction(
        ControllerCore $controller,
        string $action
    ) : bool
    {
        //Mock For App::_isValidControllerAction()

        return true;
    }

    private function _autoLoad(string $controller = '') : void
    {
        require_once __DIR__.'/autoload.php';
    }

    private function _error() : void
    {
        throw new Exception('Test App Init Eror!');
    }

    public function errorHandler(
        int    $errCode,
        string $errMessage,
        string $errFile,
        int    $errLine
    ) : void
    {
        $expMessage = $exp->getMessage();

        throw new Exception($errMessage);
    }

    private function _exception(Exception $exp) : void
    {
        $expMessage = $exp->getMessage();

        throw new Exception($expMessage);
    }

}
?>