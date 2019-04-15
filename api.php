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