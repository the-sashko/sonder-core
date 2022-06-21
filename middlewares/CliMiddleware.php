<?php

namespace Sonder\Middlewares;

use Sonder\Core\CoreMiddleware;
use Sonder\Exceptions\CoreException;
use Sonder\Interfaces\ICliMiddleware;
use Sonder\Interfaces\IMiddleware;
use Sonder\Exceptions\AppException;
use Sonder\Exceptions\MiddlewareException;

#[IMiddleware]
#[ICliMiddleware]
final class CliMiddleware extends CoreMiddleware implements ICliMiddleware
{
    /**
     * @var array
     */
    private array $_cliOptions;

    /**
     * @return void
     * @throws CoreException
     * @throws MiddlewareException
     */
    final public function run(): void
    {
        $this->_cliOptions = (array)getopt('', [
            'controller:',
            'method:',
            'params::'
        ]);

        $controller = $this->_getController();
        $controllerMethod = $this->_getControllerMethod();
        $cliValues = $this->_getCliValues();

        if (empty($controller)) {
            throw new MiddlewareException(
                MiddlewareException::MESSAGE_MIDDLEWARE_CONTROLLER_IS_NOT_SET,
                AppException::CODE_MIDDLEWARE_CONTROLLER_IS_NOT_SET,
            );
        }

        if (empty($controllerMethod)) {
            throw new MiddlewareException(
                MiddlewareException::MESSAGE_MIDDLEWARE_METHOD_IS_NOT_SET,
                AppException::CODE_MIDDLEWARE_METHOD_IS_NOT_SET,
            );
        }

        $this->request->setController($controller);
        $this->request->setControllerMethod($controllerMethod);
        $this->request->setCliValues($cliValues);
    }

    /**
     * @return string|null
     */
    private function _getController(): ?string
    {
        if (
            !array_key_exists('controller', $this->_cliOptions) ||
            empty($this->_cliOptions['controller'])
        ) {
            return null;
        }

        $controller = $this->_cliOptions['controller'];
        $controller = mb_convert_case($controller, MB_CASE_LOWER);

        $controller = preg_replace(
            '/[^a-z]/u',
            '',
            $controller
        );

        return empty($controller) ? null : $controller;
    }

    /**
     * @return string|null
     */
    private function _getControllerMethod(): ?string
    {
        if (
            !array_key_exists('method', $this->_cliOptions) ||
            empty($this->_cliOptions['method'])
        ) {
            return null;
        }

        $controllerMethod = $this->_cliOptions['method'];
        $controllerMethod = mb_convert_case($controllerMethod, MB_CASE_LOWER);
        $controllerMethod = preg_replace('/[^a-z]/u', '', $controllerMethod);
        $controllerMethod = mb_convert_case($controllerMethod, MB_CASE_TITLE);

        return empty($controllerMethod) ? null : sprintf(
            'display%s',
            $controllerMethod
        );
    }

    /**
     * @return array|null
     * @throws CoreException
     */
    private function _getCliValues(): ?array
    {
        $securityPlugin = $this->getPlugin('security');
        $cliValues = null;

        if (
            array_key_exists('params', $this->_cliOptions) &&
            !empty($this->_cliOptions['params'])
        ) {
            $cliValues = $this->_cliOptions['params'];

            parse_str($cliValues, $cliValues);
        }

        $cliValues = array_map(
            [
                $securityPlugin,
                'escapeInput'
            ],
            (array)$cliValues
        );

        return empty($cliValues) ? null : $cliValues;
    }
}
