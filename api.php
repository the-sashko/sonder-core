<?php
/**
 * Main Application Class For API Requests
 */
class API Extends App
{
    public function __construct()
    {
        $this->_validateURI();
    }

    /**
     * Handler For Only Api Class Errors
     */
    private function _error() : void
    {
        header('Location: /', true, 302);
        exit(0);
    }

    /**
     * Require All Plugins And Controller Classes
     *
     * @param string $controller Name Of Controller Class
     */
    private function _autoLoad(string $controller = '') : void
    {
        require_once __DIR__.'/autoload.php';
        require_once __DIR__.'/../controllers/'.$controller.'.php';
    }
}
?>