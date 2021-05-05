<?php
/**
 * Main Application Class
 */
class App
{
    /**
     * @var Router|null Router Instance
     */
    private $_router = null;

    public function __construct()
    {
        require_once __DIR__.'/autoload.php';

        set_exception_handler([$this, 'exceptionHandler']);
        set_error_handler([$this, 'errorHandler']);

        if (class_exists('Router')) {
            $this->_router = new Router();
        }

        $this->_redirect();
        $this->_rewriteUrl();
    }

    /**
     * Main Method For Application
     */
    public function run(): void
    {
        list(
            $controller,
            $action,
            $urlParams,
            $page,
            $language
        ) = $this->_parseUrl();

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

        $controller = (string) $controller;

        require_once __DIR__.'/../controllers/'.$controller.'.php';

        try {
            $controller = new $controller($urlParams, $page, $language);

            if (!$this->isValidControllerAction($controller, $action)) {
                $errorMessage = '%s. Controller: %s. Action: %s';

                $errorMessage = sprintf(
                    $errorMessage,
                    CoreException::MESSAGE_CORE_INVALID_ACTION_CONTROLLER,
                    get_class($controller),
                    $action
                );

                throw new CoreException(
                    $errorMessage,
                    CoreException::CODE_CORE_INVALID_ACTION_CONTROLLER
                );
            }

            $controller->$action();
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
     * @param string|null         $action     Name Of Method
     *
     * @return bool Is Method Public And Exists In Controller
     */
    protected function isValidControllerAction(
        ?ControllerCore $controller = null,
        ?string         $action     = null
    ): bool
    {
        if (empty($controller) || empty($action)) {
            return false;
        }

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
     * Perform Redirects By Rules
     */
    private function _redirect(): void
    {
        $_SERVER['REQUEST_URI'] = parse_url(
            $_SERVER['REQUEST_URI'],
            PHP_URL_PATH
        );

        $url = $_SERVER['REQUEST_URI'];
        $url = explode('&', $url);
        $url = array_shift($url);
        $url = explode('=', $url);
        $url = array_shift($url);

        if ($_SERVER['REQUEST_URI'] != $url) {
            header("Location: {$url}", true, 301);
            exit(0);
        }


        if (!empty($this->_router)) {
            $this->_router->routeRedirect($_SERVER['REQUEST_URI']);
        }
    }

    /**
     * Rewrite URL By Rules
     */
    private function _rewriteUrl(): void
    {
        $_SERVER['REAL_REQUEST_URI'] = $_SERVER['REQUEST_URI'];

        $url = $_SERVER['REQUEST_URI'];

        if (empty($url)) {
            $url = '/';
        }

        if (!empty($this->_router)) {
            $url = $this->_router->routeRewrite($url);
        }

        $_SERVER['REQUEST_URI'] = $url;
    }

    /**
     * Parse Controller, Method Of Cotroller And Params From URL
     */
    private function _parseUrl(): ?array
    {
        $language   = null;
        $controller = null;
        $action     = null;
        $urlParams  = null;
        $page       = null;

        $urlParams = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        parse_str($urlParams, $urlParams);

        $_SERVER['REQUEST_URI'] = parse_url(
            $_SERVER['REQUEST_URI'],
            PHP_URL_PATH
        );

        $url = (string) $_SERVER['REQUEST_URI'];

        if (preg_match('/^\/(.*?)\/page-([0-9]+)\/$/su', $url)) {
            $page = preg_replace('/^\/(.*?)\/page-(.*?)\/$/su', '$2', $url);
            $url  = preg_replace('/^\/(.*?)\/page-(.*?)\/$/su', '$1', $url);
        }

        $url = preg_replace('/((^\/)|(\/$))/su', '', $url);

        $urlData = explode('/', $url);

        if (!empty($urlData)) {
            $language = array_shift($urlData);
        }

        if (!empty($urlData)) {
            $controller = array_shift($urlData);
        }

        if (!empty($urlData)) {
            $action = array_shift($urlData);
        }

        if (null === $controller) {
            throw new CoreException(
                CoreException::MESSAGE_CORE_CONTROLLER_IS_NOT_SET,
                CoreException::CODE_CORE_CONTROLLER_IS_NOT_SET
            );
        }

        if (null === $action) {
            throw new CoreException(
                CoreException::MESSAGE_CORE_ACTION_CONTROLLER_IS_NOT_SET,
                CoreException::CODE_CORE_ACTION_CONTROLLER_IS_NOT_SET
            );
        }

        $language   = (string) $language;
        $controller = mb_convert_case($controller, MB_CASE_TITLE).'Controller';
        $action     = 'display'.mb_convert_case($action, MB_CASE_TITLE);
        $urlParams  = (array) $urlParams;
        $page       = (int) $page;

        return [
            $controller,
            $action,
            $urlParams,
            $page,
            $language
        ];
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
