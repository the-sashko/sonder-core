<?php
/**
 * Main Application Class 
 */
class App
{
    use Router;

    public function __construct()
    {
        $this->_redirect();
        $this->_replaceURI();
    }

    /**
     * Main Method For Application
     */
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

    /**
     * Errors Handler
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

    /**
     * Perform Redirects By Rules
     */
    private function _redirect() : void
    {
        $uri = $_SERVER['REQUEST_URI'];

        $this->routeRedirect($uri);
    }

    /**
     * Rewrite URI By Rules
     */
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

    /**
     * Parse Controller, Method Of Cotroller And Params From URI
     */
    private function _parseURI() : array
    {
        $page = NULL;
        $param = NULL;
        $controller = NULL;
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

        if ($controller === NULL || $action === NULL) {
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

    /**
     * Check Is Controller Exists
     *
     * @param string $controller Var
     *
     * @return bool Is Controller Exists
     */
    private function _isControllerExist(string $controller = '') : bool
    {
        return file_exists(__DIR__.'/../controllers/'.$controller.'.php');
    }

    /**
     * Check Is Method Public And Exists In Controller 
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
        if (!method_exists($controller, $action)) {
            return false;
        }

        $reflection = new ReflectionMethod($controller, $action);

        if (!$reflection->isPublic()) {
            return false;
        }

        return true;
    }

    /**
     * Handler For Only App Class Errors
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

    /**
     * Exceptions Handler
     *
     * @param Exception $exp Exception Instance
     */
    private function _exception(Exception $exp = NULL) : void
    {
        $expMessage = $exp->getMessage();

        (new ErrorPlugin)->displayException($expMessage, OUTPUT_FORMAT_JSON);

        (new LoggerPlugin)->logError($expMessage);

        exit(0);
    }
}
?>