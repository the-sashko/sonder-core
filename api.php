<?php
/**
 * Main Application Class For API Requests
 */
class API
{
    public function __construct()
    {
        $this->_validateURI();
    }

    /**
     * Main Method For Application
     */
    public function run() : void
    {
        try {
            list(
                $apiVersion,
                $model,
                $action,
                $params,
                $page
            ) = $this->_getParams($apiVersion,);

            $this->_autoLoad();

            $controller = new APIController($params, $page);
            
            set_error_handler([$this, 'errorHandler']);

            $controller->execute($model, $action, );
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
     * Parse Controller, Method Of Cotroller And Params From URI
     */
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

    /**
     * Exceptions Handler
     *
     * @param Exception $exp Exception Instance
     */
    private function _exception(Exception $exp) : void
    {
        $expMessage = $exp->getMessage();

        (new ErrorPlugin)->displayException($expMessage, OUTPUT_FORMAT_JSON);

        (new LoggerPlugin)->logError($expMessage);

        exit(0);
    }
}
?>