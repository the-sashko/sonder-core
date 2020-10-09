<?php
/**
 * Application Class For Deamon Workers
 */
class CLI extends App
{
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
            $cliParams
        ) = $this->_parseCLIOptions();

        if (!$this->isControllerExist($controller)) {
            $errorMessage = '%s. Controller: %s';

            $errorMessage = sprintf(
                $errorMessage,
                AppException::MESSAGE_APP_CONTROLLER_IS_NOT_EXIST,
                $controller
            );

            throw new AppException(
                $errorMessage,
                AppException::CODE_APP_CONTROLLER_IS_NOT_EXIST
            );
        }

        require_once __DIR__.'/../controllers/'.$controller.'.php';

        try {
            $controller = new $controller($cliParams);

            if (!$this->isValidControllerAction($controller, $action)) {
                $errorMessage = '%s. Controller: %s. Action: %s';

                $errorMessage = sprintf(
                    $errorMessage,
                    AppException::MESSAGE_APP_INVALID_ACTION_CONTROLLER,
                    get_class($controller),
                    $action
                );

                throw new AppException(
                    $errorMessage,
                    AppException::CODE_APP_INVALID_ACTION_CONTROLLER
                );
            }

            set_error_handler([$this, 'errorHandler']);
            $controller->$action();
        } catch (Exception $exp) {
            $this->exceptionHandler($exp);
        }

        exit(0);
    }

    private function _parseCLIOptions(): array
    {
        $cliOptions = getopt('', ['controller:', 'action:', 'params:']);

        if (!array_key_exists('controller', $cliOptions)) {
            throw new AppException(
                AppException::MESSAGE_APP_CONTROLLER_IS_NOT_SET,
                AppException::CODE_APP_CONTROLLER_IS_NOT_SET
            );
        }

        if (!array_key_exists('action', $cliOptions)) {
            throw new AppException(
                AppException::MESSAGE_APP_ACTION_CONTROLLER_IS_NOT_SET,
                AppException::CODE_APP_ACTION_CONTROLLER_IS_NOT_SET
            );
        }

        if (!array_key_exists('params', $cliOptions)) {
            $cliOptions['params'] = null;
        }

        $controller = $cliOptions['controller'];
        $action     = $cliOptions['action'];
        $cliParams  = $cliOptions['params'];

        $controller = mb_convert_case($controller, MB_CASE_TITLE).'Controller';
        $action     = 'action'.mb_convert_case($action, MB_CASE_TITLE);
        parse_str($cliParams, $cliParams);

        return [
            $controller,
            $action,
            $cliParams
        ];
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
}
