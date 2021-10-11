<?php

namespace Sonder;

use Throwable;

final class App
{
    /**
     * @var string
     */
    private string $_endpointName = 'app';

    /**
     * @var array|null
     */
    private ?array $_middlewares = null;

    final public function __construct()
    {
        if (defined('APP_ENDPOINT')) {
            $this->_endpointName = APP_ENDPOINT;
        }

        if (defined('APP_MIDDLEWARES')) {
            $this->_middlewares = APP_MIDDLEWARES;
        }

        set_exception_handler([$this, 'exceptionHandler']);
        set_error_handler([$this, 'errorHandler']);
    }

    final public function run(): void
    {
        try {
            $endpointClass = sprintf(
                '\Sonder\Endpoints\%sEndpoint',
                mb_convert_case($this->_endpointName, MB_CASE_TITLE)
            );

            $endpoint = new $endpointClass();

            $endpoint->run($this->_middlewares);
        } catch (Throwable $exp) {
            $this->exceptionHandler($exp);
        }
    }

    final public function errorHandler(
        int    $errorCode,
        string $errorMessage,
        string $errorFile,
        int    $errorLine
    ): void
    {
        $debugBacktrace = $this->_getDebugBacktrace();

        $logMessage = sprintf(
            'Error #%d: %s. File: %s (%d). Trace: %s',
            $errorCode,
            $errorMessage,
            $errorFile,
            $errorLine,
            implode(' -> ', array_reverse($debugBacktrace))
        );

        //TODO

        //TMP
        echo $logMessage;
        //END TMP

        exit(0);
    }

    final public function exceptionHandler(Throwable $exception): void
    {
        $debugBacktrace = $this->_getDebugBacktrace();

        $logMessage = sprintf(
            'Error #%d: %s. File: %s (%d). Trace: %s',
            $exception->getCode(),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            implode(' -> ', array_reverse($debugBacktrace))
        );

        $logName = get_class($exception);
        $logName = explode('\\', $logName);
        $logName = (string)end($logName);

        $logName = preg_replace(
            '/^(.*?)Exception$/sui',
            '$1',
            $logName
        );

        //TODO

        //TMP
        echo $logMessage;
        //END TMP

        exit(0);
    }

    private function _getDebugBacktrace(): array
    {
        $debugBacktrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        if (!empty($debugBacktrace)) {
            array_shift($debugBacktrace);
        }

        foreach ($debugBacktrace as $key => $debugBacktraceStep) {
            $debugBacktrace[$key] = '…';

            if (array_key_exists('file', $debugBacktraceStep)) {
                $debugBacktrace[$key] = $debugBacktraceStep['file'];
            }

            if (array_key_exists('line', $debugBacktraceStep)) {
                $debugBacktrace[$key] = sprintf(
                    '%s (%d)',
                    $debugBacktrace[$key],
                    $debugBacktraceStep['line']
                );
            }
        }

        return $debugBacktrace;
    }
}
