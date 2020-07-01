<?php
/**
 * Application Class For Deamon Workers
 */
class CLI extends App
{
    const DEFAULT_PAGE = 1;

    const DEFAULT_POST_ARRAY = [];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Main Method For Application Test
     */
    public function run(): void
    {
        list(
            $controller,
            $action,
            $param
        ) = $this->_parseCLIOptions();

        if (!$this->_isControllerExist($controller)) {
            $errorMessage = sprintf('Controller %s Not Found', $controller);
            throw new Exception($errorMessage);
        }

        $this->_autoLoad($controller);

        $controller = new $controller(
            $param,
            static::DEFAULT_POST_ARRAY,
            static::DEFAULT_PAGE
        );

        if (!$this->_isValidControllerAction($controller, $action)) {
            throw new Exception(sprintf('Not Fount Valid Action %s', $action));
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
     * Mock Method Of Performing Redirects By Rules
     */
    private function _redirect(): void
    {
        //Mock For App::_redirect()
    }

    /**
     * Mock Rewrite URI By Rules Method
     */
    private function _replaceURI(): void
    {
        //Mock For App::_replaceURI()
    }

    /**
     *  Mock Parsing URI Method
     */
    private function _parseURI(): ?array
    {
        //Mock For App::_parseURI()

        return null;
    }

    private function _parseCLIOptions(): array
    {
        $cliOptions = getopt('', ['controller:', 'action:', 'param:']);

        if (!array_key_exists('controller', $cliOptions)) {
            throw new Exception('Missing Controller Option');
        }

        if (!array_key_exists('action', $cliOptions)) {
            throw new Exception('Missing Action Option');
        }

        if (!array_key_exists('param', $cliOptions)) {
            $cliOptions['param'] = null;
        }

        return [
            mb_convert_case($cliOptions['controller'], MB_CASE_TITLE).
                'Controller',
            'action'.
                mb_convert_case($cliOptions['action'], MB_CASE_TITLE),
            (string) $cliOptions['param']
        ];
    }

    /**
     * Require All Plugins
     *
     * @param string|null $controller Name Of Controller Class
     */
    private function _autoLoad(?string $controller = null): void
    {
        if (empty($controller)) {
            throw new Exception('CLI Controller Is Not Set');
        }

        require_once __DIR__.'/autoload.php';
        require_once __DIR__.'/../controllers/'.$controller.'.php';
    }

    /**
     *  Mock Redirect Rules
     */
    public function routeRedirect(?string $uri = null): void
    {
        //Mock For App::routeRedirect()
    }

    /**
     *  Mock Rewrite Rules
     */
    public function routeRewrite(?string $uri = null): ?string
    {
        //Mock For App::routeRewrite()

        return null;
    }

    /**
     * Check Is Controller Exists
     *
     * @param string|null $controller Var
     *
     * @return bool Is Controller Exists
     */
    private function _isControllerExist(?string $controller = null): bool
    {
        if (empty($controller)) {
            return false;
        }

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
    ): bool
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
     * Exceptions Handler
     *
     * @param Exception|null $exp Exception Instance
     */
    private function _exception(?Exception $exp = null): void
    {
        $expMessage = $exp->getMessage();

        (new ErrorPlugin)->displayException($expMessage, true);

        (new LoggerPlugin)->logError($expMessage);

        exit(0);
    }
}
