<?php
/**
 * Main Application Class
 */
class App
{
    public function __construct()
    {
        require_once __DIR__.'/autoload.php';

        set_exception_handler([$this, 'exceptionHandler']);
        set_error_handler([$this, 'errorHandler']);

        $_SERVER['REAL_REQUEST_URI'] = $_SERVER['REQUEST_URI'];
    }

    /**
     * Main Method For Application
     */
    public function run(): void
    {
        $urlRoute = $this->_parseUrl();

        if (empty($urlRoute)) {
            $this->_notFoundHandler();
        }

        $controller = $urlRoute->getController();
        $method     = $urlRoute->getMethod();
        $params     = $urlRoute->getParams();
        $page       = $urlRoute->getPage();
        $language   = $urlRoute->getLanguage();

        if (!$this->isControllerExist($controller)) {
            $errorMessage = '%s. Controller: %s';

            $errorMessage = sprintf(
                $errorMessage,
                CoreException::MESSAGE_CORE_CONTROLLER_IS_NOT_EXIST,
                $controller
            );

            throw new CoreException(
                $errorMessage,
                CoreException::CODE_CORE_CONTROLLER_IS_NOT_EXIST
            );
        }

        require_once __DIR__.'/../controllers/'.$controller.'.php';

        try {
            $controller = new $controller($params, $page, $language);

            if (!$this->isValidControllerMethod($controller, $method)) {
                $errorMessage = '%s. Controller: %s. Action: %s';

                $errorMessage = sprintf(
                    $errorMessage,
                    CoreException::MESSAGE_CORE_INVALID_ACTION_CONTROLLER,
                    get_class($controller),
                    $method
                );

                throw new CoreException(
                    $errorMessage,
                    CoreException::CODE_CORE_INVALID_ACTION_CONTROLLER
                );
            }

            $controller->$method();
        } catch (\Throwable $exp) {
            $this->exceptionHandler($exp);
        }

        exit(0);
    }

    /**
     * Errors Handler
     *
     * @param int    $errorCode    HTTP Response Code
     * @param string $errorMessage Error Message
     * @param string $errorFile    File With Error
     * @param int    $errorLine    Line In File With Error
     */
    public function errorHandler(
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

        (new LoggerPlugin)->logError($logMessage);

        (new ErrorPlugin)->displayError(
            $errorCode,
            $errorMessage,
            $errorFile,
            $errorLine,
            $debugBacktrace,
            OUTPUT_FORMAT
        );

        exit(0);
    }

    /**
     * Exceptions Handler
     *
     * @param Throwable|null $exception Exception Instance
     */
    public function exceptionHandler(?Throwable $exception = null): void
    {
        if (empty($exception)) {
            exit(0);
        }

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
        $logName = (string) end($logName);
        $logName = preg_replace('/^(.*?)Exception$/sui', '$1', $logName);

        (new LoggerPlugin)->logError($logMessage, $logName);

        (new ErrorPlugin)->displayError(
            $exception->getCode(),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $debugBacktrace,
            OUTPUT_FORMAT
        );

        exit(0);
    }

    /**
     * Check Is Method Public And Exists In Controller
     *
     * @param ControllerCore|null $controller ControllerCore Instance
     * @param string|null         $method     Name Of Method
     *
     * @return bool Is Method Public And Exists In Controller
     */
    protected function isValidControllerMethod(
        ?ControllerCore $controller = null,
        ?string         $method     = null
    ): bool
    {
        if (empty($controller) || empty($method)) {
            return false;
        }

        if (!method_exists($controller, $method)) {
            return false;
        }

        $reflection = new ReflectionMethod($controller, $method);

        if (!$reflection->isPublic()) {
            return false;
        }

        return true;
    }

    /**
     * Check Is Controller Exists
     *
     * @param string|null $controller Var
     *
     * @return bool Is Controller Exists
     */
    protected function isControllerExist(?string $controller = null): bool
    {
        if (empty($controller)) {
            return false;
        }

        return file_exists(__DIR__.'/../controllers/'.$controller.'.php');
    }

    /**
     * Parse Controller, Method Of Cotroller And Params From URL
     */
    private function _parseUrl(): ?Core\Plugins\Router\Classes\RouterEntity
    {
        $urlParams = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        parse_str($urlParams, $urlParams);

        $_SERVER['REQUEST_URI'] = parse_url(
            $_SERVER['REQUEST_URI'],
            PHP_URL_PATH
        );

        $url = (string) $_SERVER['REQUEST_URI'];

        $routerPlugin = new RouterPlugin();

        $urlRoute = $routerPlugin->getRoute($url);

        if (empty($urlRoute)) {
            return null;
        }

        $urlRoute->setParams($urlParams);

        return $urlRoute;
    }

    /**
     * URL Not Found Handler 
     */
    private function _notFoundHandler(): void
    {
        if (
            defined('APP_NOT_FOUND_URL') &&
            !empty(APP_NOT_FOUND_URL) &&
            $_SERVER['REQUEST_URI'] != APP_NOT_FOUND_URL
        ) {
            header(sprintf('Location: %s', APP_NOT_FOUND_URL));
            exit(0);
        }

        header('HTTP/1.1 404 Not Found');

        $errorPLugin = new ErrorPlugin();

        $errorCode    = ErrorPlugin::HTTP_NOT_FOUND;
        $errorMessage = $errorPLugin->getHttpErrorMessage($errorCode);

        $errorPLugin->handleHttpError($errorCode);

        echo sprintf('Error %d: %s', $errorCode, $errorMessage);

        exit(0);
    }

    /**
     * Get Backtrace For Debug
     *
     * @return array Backtrace
     */
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
