<?php
class App
{

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

        if (!$this->_isValidControllerAction($controller, $action)) {
            $this->_error();
        }
            
        try {
            set_error_handler([$this, 'errorHandler']);
            $controller->$action();
        } catch (\Exception $exp) {
            $this->_exception($exp);
        }

        exit(0);
    }

    public function errorHandler(
        int    $errCode,
        string $errMessage,
        string $errFile,
        int    $errLine
    ) : void {
        $debugBacktrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        foreach ($debugBacktrace as $idx => $debugBacktraceStep) {
            if (!array_key_exists('file', $debugBacktraceStep)) {
                $debugBacktrace[$idx] = '...';
            } else {
                $debugBacktrace[$idx] = $debugBacktraceStep['file'];
            }

            if (array_key_exists('line', $debugBacktraceStep)) {
                $debugBacktrace[$idx] = $debugBacktrace[$idx].
                                        ' ('.$debugBacktraceStep['line'].')';
            }
        }

        $debugBacktraceStr = implode(' -> ', array_reverse($debugBacktrace));
        $logMessage = "Error [$errCode]: $errMessage. ".
                      "File: $errFile ($errLine). ".
                      "Trace: $errFile ($debugBacktraceStr)";

        (new ErrorPlugin)->displayError(
            $errCode,
            $errMessage,
            $errFile,
            $errLine,
            $debugBacktrace,
            OUTPUT_FORMAT_JSON
        );

        (new LoggerPlugin)->logError($logMessage);

        exit(0);
    }

    private function _redirect() : void
    {
        $uri = $_SERVER['REQUEST_URI'];

        $this->routeRedirect($uri);
    }

    private function _replaceURI() : void
    {
        $_SERVER['REAL_REQUEST_URI'] = $_SERVER['REQUEST_URI'];
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
        return file_exists(__DIR__.'/../controllers/'.$controller.'.php');
    }

    private function _isValidControllerAction(
        ControllerCore $controller,
        string $action
    ) : bool
    {
        if (!method_exists($controller, $action)) {
            return false;
        }

        $reflection = new ReflectionMethod($controller, $action);

        if (!$reflection->isPublic()) {
            return false;
        }

        return true;
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
        $expMessage = $exp->getMessage();

        (new ErrorPlugin)->displayException($expMessage, OUTPUT_FORMAT_JSON);

        (new LoggerPlugin)->logError($expMessage);

        exit(0);
    }
}
?>