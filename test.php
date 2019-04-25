<?php
/**
 * Application Class For Unit Tests
 */
class Test extends App
{
    public function __construct()
    {
        parent::__construct();
        session_start();
    }

    /**
     * Main Method For Application Test
     */
    public function run() : void
    {
        $this->_autoload();
    }

    /**
     * Errors Handler Method
     *
     * @param int    $errCode    HTTP Response Code
     * @param string $errMessage Error Message
     * @param string $errFile    File With Error
     * @param int    $errLine    Line In File With Error
     */
    public function errorHandler(
        int    $errCode,
        string $errMessage,
        string $errFile,
        int    $errLine
    ) : void
    {
        throw new Exception($errMessage);
    }

    /**
     * Mock Method Of Performing Redirects By Rules
     */
    private function _redirect() : void
    {
        //Mock For App::_redirect()
    }

    /**
     * Mock Rewrite URI By Rules Method
     */
    private function _replaceURI() : void
    {
        //Mock For App::_replaceURI()
    }

    /**
     *  Mock Parsing URI Method
     */
    private function _parseURI() : array
    {
        //Mock For App::_parseURI()

        return [];
    }

    /**
     * Mock Checking Controller Existsting Method
     */
    private function _isControllerExist(string $controller = '') : bool
    {
        //Mock For App::_isControllerExist()

        return TRUE;
    }

    /**
     * Mock Method Of Validation Methods
     *
     * @param ControllerCore $controller ControllerCore Instance
     * @param string         $action     Name Of Method
     *
     * @return bool Is Method Public And Exists In Controller
     */
    private function _isValidControllerAction(
        ControllerCore $controller,
        string         $action
    ) : bool
    {
        //Mock For App::_isValidControllerAction()

        return TRUE;
    }

    /**
     * Handler For Only Test Class Errors
     */
    private function _error() : void
    {
        throw new Exception('Test App Init Eror!');
    }

    /**
     * Require All Plugins
     *
     * @param string $controller Name Of Controller Class
     */
    private function _autoLoad(string $controller = '') : void
    {
        require_once __DIR__.'/autoload.php';
    }

    /**
     * Exceptions Handler
     *
     * @param Exception $exp Exception Instance
     */
    private function _exception(Exception $exp) : void
    {
        $expMessage = $exp->getMessage();

        throw new Exception($expMessage);
    }
}
?>
