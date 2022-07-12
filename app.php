<?php

namespace Sonder;

use Exception;
use Sonder\Core\CoreEvent;
use Sonder\Core\CoreObject;
use Sonder\Core\Interfaces\ICoreException;
use Sonder\Enums\EventTypesEnum;
use Sonder\Enums\HttpCodesEnum;
use Sonder\Exceptions\CoreException;
use Sonder\Plugins\ErrorPlugin;
use Sonder\Plugins\LoggerPlugin;
use Throwable;

final class App
{
    /**
     * @var string
     */
    private string $_endpointName = 'app';

    final public function __construct()
    {
        if (defined('APP_ENDPOINT')) {
            $this->_endpointName = APP_ENDPOINT;
        }

        set_exception_handler([$this, 'exceptionHandler']);
        set_error_handler([$this, 'errorHandler']);
    }

    /**
     * @throws Exception
     */
    final public function run(): void
    {
        try {
            (new CoreEvent)->run(EventTypesEnum::APP_RUN, []);

            $endpointClass = sprintf(
                '\Sonder\Endpoints\%sEndpoint',
                mb_convert_case($this->_endpointName, MB_CASE_TITLE)
            );

            $endpoint = new $endpointClass();

            $endpoint->run();
        } catch (Throwable $thr) {
            $this->exceptionHandler($thr);
        }
    }

    /**
     * @param int $errorCode
     * @param string $errorMessage
     * @param string $errorFile
     * @param int $errorLine
     * @return never
     * @throws CoreException
     */
    final public function errorHandler(
        int $errorCode,
        string $errorMessage,
        string $errorFile,
        int $errorLine
    ): never {
        /* @var $loggerPlugin LoggerPlugin */
        $loggerPlugin = CoreObject::getPlugin('logger', APP_RESPONSE_FORMAT);

        /* @var $errorPlugin ErrorPlugin */
        $errorPlugin = CoreObject::getPlugin('error', APP_RESPONSE_FORMAT);

        $debugBacktrace = $this->_getDebugBacktrace();

        $logMessage = sprintf(
            'Error #%d: %s. File: %s (%d). Trace: %s',
            $errorCode,
            $errorMessage,
            $errorFile,
            $errorLine,
            implode(' -> ', array_reverse($debugBacktrace))
        );

        $loggerPlugin->logError($logMessage);

        $errorPlugin->displayError(
            $errorCode,
            $errorMessage,
            $errorFile,
            $errorLine,
            $debugBacktrace,
            HttpCodesEnum::INTERNAL_SERVER_ERROR->value
        );

        exit(0);
    }

    /**
     * @param Throwable $exception
     * @return never
     * @throws CoreException
     */
    final public function exceptionHandler(Throwable $exception): never
    {
        /* @var $loggerPlugin LoggerPlugin */
        $loggerPlugin = CoreObject::getPlugin('logger');

        /* @var $errorPlugin ErrorPlugin */
        $errorPlugin = CoreObject::getPlugin('error', APP_RESPONSE_FORMAT);

        $debugBacktrace = $exception->getTrace();

        $debugBacktrace = array_map(
            function ($traceRow) {
                if (!array_key_exists('file', $traceRow)) {
                    return '...';
                }

                $line = '';

                if (isset($traceRow['line'])) {
                    $line = sprintf(' (%d)', (int)$traceRow['line']);
                }

                return sprintf('%s%s', $traceRow['file'], $line);
            },

            $debugBacktrace
        );

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

        if (empty($logName)) {
            $logName = $loggerPlugin::DEFAULT_ERROR_LOG_NAME;
        }

        $logName = preg_replace(
            '/([^A-Za-z])/su',
            '_',
            $logName
        );

        $logName = preg_replace(
            '/([A-Z])/su',
            '_$1',
            $logName
        );

        $logName = preg_replace('/([_]+)/su', '_', $logName);

        $logName = preg_replace(
            '/((^_)|(_$))/su',
            '',
            $logName
        );

        $logName = mb_convert_case($logName, MB_CASE_LOWER);

        $loggerPlugin->logError($logMessage, $logName);

        $httpResponseCode = HttpCodesEnum::INTERNAL_SERVER_ERROR->value;

        if ($exception instanceof ICoreException) {
            $httpResponseCode = $exception->getHttpResponseCode();
        }

        $errorPlugin->displayError(
            $exception->getCode(),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $debugBacktrace,
            $httpResponseCode
        );

        exit(0);
    }

    /**
     * @return array
     */
    private function _getDebugBacktrace(): array
    {
        $debugBacktrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        if (!empty($debugBacktrace)) {
            array_shift($debugBacktrace);
        }

        foreach ($debugBacktrace as $key => $debugBacktraceStep) {
            $debugBacktrace[$key] = '…';

            if (isset($debugBacktraceStep['file'])) {
                $debugBacktrace[$key] = $debugBacktraceStep['file'];
            }

            if (isset($debugBacktraceStep['line'])) {
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
