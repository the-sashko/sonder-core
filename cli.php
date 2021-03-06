<?php
/**
 * Application Class For Deamon Workers
 */
class Cli extends App
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
        ) = $this->_parseCliOptions();

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
            $controller = new $controller($cliParams);

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

            set_error_handler([$this, 'errorHandler']);
            $controller->$action();
        } catch (Exception $exp) {
            $this->exceptionHandler($exp);
        }

        exit(0);
    }

    private function _parseCliOptions(): array
    {
        $cliOptions = getopt('', ['controller:', 'action:', 'params:']);

        if (!array_key_exists('controller', $cliOptions)) {
            throw new CoreException(
                CoreException::MESSAGE_CORE_CONTROLLER_IS_NOT_SET,
                CoreException::CODE_CORE_CONTROLLER_IS_NOT_SET
            );
        }

        if (!array_key_exists('action', $cliOptions)) {
            throw new CoreException(
                CoreException::MESSAGE_CORE_ACTION_CONTROLLER_IS_NOT_SET,
                CoreException::CODE_CORE_ACTION_CONTROLLER_IS_NOT_SET
            );
        }

        if (!array_key_exists('params', $cliOptions)) {
            $cliOptions['params'] = null;
        }

        $controller = (string) $cliOptions['controller'];
        $action     = (string) $cliOptions['action'];
        $cliParams  = (string) $cliOptions['params'];

        $controller = mb_convert_case($controller, MB_CASE_TITLE).'Controller';
        $action     = 'action'.mb_convert_case($action, MB_CASE_TITLE);
        parse_str($cliParams, $cliParams);

        return [
            $controller,
            $action,
            $cliParams
        ];
    }
}
