<?php

namespace Sonder\Middlewares;

use Exception;
use Sonder\Core\CoreMiddleware;
use Sonder\Core\Interfaces\IMiddleware;
use Sonder\Exceptions\AppException;
use Sonder\Exceptions\MiddlewareException;

final class CliMiddleware extends CoreMiddleware implements IMiddleware
{
    /**
     * @var array
     */
    private array $_cliOptions;

    /**
     * @throws Exception
     */
    final public function run(): void
    {
        $this->_cliOptions = (array)getopt('', [
            'controller:',
            'method:',
            'params::'
        ]);

        $controller = $this->_getController();
        $method = $this->_getMethod();
        $cliValues = $this->_getCliValues();

        if (empty($controller)) {
            throw new MiddlewareException(
                MiddlewareException::MESSAGE_MIDDLEWARE_CONTROLLER_IS_NOT_SET,
                AppException::CODE_MIDDLEWARE_CONTROLLER_IS_NOT_SET,
            );
        }

        if (empty($method)) {
            throw new MiddlewareException(
                MiddlewareException::MESSAGE_MIDDLEWARE_METHOD_IS_NOT_SET,
                AppException::CODE_MIDDLEWARE_METHOD_IS_NOT_SET,
            );
        }

        $this->request->setController($controller);
        $this->request->setMethod($method);
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
            '/[^a-z]/su',
            '',
            $controller
        );

        return empty($controller) ? null : $controller;
    }

    /**
     * @return string|null
     */
    private function _getMethod(): ?string
    {
        if (
            !array_key_exists('method', $this->_cliOptions) ||
            empty($this->_cliOptions['method'])
        ) {
            return null;
        }

        $method = $this->_cliOptions['method'];
        $method = mb_convert_case($method, MB_CASE_LOWER);
        $method = preg_replace('/[^a-z]/su', '', $method);
        $method = mb_convert_case($method, MB_CASE_TITLE);

        return empty($method) ? null : sprintf('display%s', $method);
    }

    /**
     * @return array|null
     *
     * @throws Exception
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
