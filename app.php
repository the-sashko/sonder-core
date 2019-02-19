<?php
class App {

    use Router;

    public function __construct()
    {
        $this->_redirect();
        $this->_replaceURI();
    }

    public function run() : void
    {
        list(
            $controller,
            $action,
            $param,
            $page
        ) = $this->_parseURI();

        if (!$this->_isControllerExist($controller)) {
            $this->_error();
        }

        $this->_autoLoad($controller);

        $controller = new $controller(
            $param,
            $_POST,
            $page
        );

        try {
            $controller->$action();
        } catch (\Exception $exp) {
            $this->_exception($exp);
        }

        exit(0);
    }

    private function _redirect() : void
    {
        $uri = $_SERVER['REQUEST_URI'];

        $this->routeRedirect($uri);
    }

    private function _replaceURI() : void
    {
        $uri = $_SERVER['REQUEST_URI'];

        if ($uri == '') {
            $uri = '/';
        }

        $uri = $this->routeRewrite($uri);

        $_SERVER['REQUEST_URI'] = $uri;
    }

    private function _parseURI() : array
    {
        $page = null;
        $param = null;
        $controller = null;
        $uri = $_SERVER['REQUEST_URI'];

        if (preg_match('/^\/(.*?)\/page-([0-9]+)\/$/su', $uri)) {
            $page = preg_replace(
                '/^\/(.*?)\/page-([0-9]+)\/$/su',
                '$2',
                $uri
            );
            $uri = preg_replace(
                '/^\/(.*?)\/page-([0-9]+)\/$/su',
                '$1',
                $uri
            );
        }

        $uri = preg_replace('/^\/(.*?)\/$/su', '$1', $uri);

        $uriData = explode('/', $uri);

        if (isset($uriData[2])) {
            $param = $uriData[2];
        }

        if (isset($uriData[1])) {
            $action = $uriData[1];
        }

        if (isset($uriData[0])) {
            $controller = $uriData[0];
        }

        if ($controller === null || $action === null) {
            $this->_error();
        }

        $controller = mb_convert_case($controller, MB_CASE_TITLE).'Controller';
        $action = 'action'.mb_convert_case($action, MB_CASE_TITLE);

        $param = (string) $param;
        $page = (int) $page;

        return [
            $controller,
            $action,
            $param,
            $page
        ];
    }

    private function _isControllerExist(string $controller = '') : bool
    {
        return is_file(__DIR__.'/../controllers/'.$controller.'.php');
    }

    private function _error() : void
    {
        header('Location: /', true, 302);
        exit(0);
    }

    private function _autoLoad(string $controller = '') : void
    {
        require_once __DIR__.'/autoload.php';
        require_once __DIR__.'/../controllers/'.$controller.'.php';
    }

    private function _exception(Exception $exp) : void
    {
        echo $exp->getMessage();
        exit(0);
    }
}
?>