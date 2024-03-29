<?php

namespace Sonder\Core;

use Sonder\Core\Interfaces\IAutoloadCore;

#[IAutoloadCore]
final class AutoloadCore implements IAutoloadCore
{
    /**
     * @var string[]
     */
    private array $_endpointPaths;

    /**
     * @var string[]
     */
    private array $_middlewarePaths;

    /**
     * @var string[]
     */
    private array $_controllerPaths;

    final public function __construct()
    {
        $endpointsPaths = [
            APP_PROTECTED_DIR_PATH . '/endpoints',
            APP_FRAMEWORK_DIR_PATH . '/endpoints'
        ];

        if (
            array_key_exists('endpoints', APP_SOURCE_PATHS) &&
            is_array(APP_SOURCE_PATHS['endpoints'])
        ) {
            $endpointsPaths = APP_SOURCE_PATHS['endpoints'];
        }

        $middlewaresPaths = [
            APP_PROTECTED_DIR_PATH . '/middlewares',
            APP_FRAMEWORK_DIR_PATH . '/middlewares'
        ];

        if (
            array_key_exists('middlewares', APP_SOURCE_PATHS) &&
            is_array(APP_SOURCE_PATHS['middlewares'])
        ) {
            $middlewaresPaths = APP_SOURCE_PATHS['middlewares'];
        }

        $controllersPaths = [
            APP_PROTECTED_DIR_PATH . '/controllers',
            APP_FRAMEWORK_DIR_PATH . '/controllers'
        ];

        if (
            array_key_exists('controllers', APP_SOURCE_PATHS) &&
            is_array(APP_SOURCE_PATHS['controllers'])
        ) {
            $controllersPaths = APP_SOURCE_PATHS['controllers'];
        }

        $this->_endpointPaths = $endpointsPaths;
        $this->_middlewarePaths = $middlewaresPaths;
        $this->_controllerPaths = $controllersPaths;
    }

    /**
     * @param string|null $className
     * @return bool
     */
    final public function load(?string $className = null): bool
    {
        $classHierarchy = explode('\\', (string)$className);

        if (empty($classHierarchy) || count($classHierarchy) < 2) {
            return false;
        }

        $classHierarchy = array_reverse($classHierarchy);

        $className = array_shift($classHierarchy);

        $classType = array_shift($classHierarchy);
        $classType = mb_convert_case($classType, MB_CASE_LOWER);

        return match ($classType) {
            'endpoints' => $this->_loadEndpoint($className),
            'middlewares' => $this->_loadMiddleware($className),
            'controllers' => $this->_loadController($className),
            default => false
        };
    }

    /**
     * @param string|null $className
     * @return bool
     */
    private function _loadEndpoint(?string $className = null): bool
    {
        if (empty($className) || empty($this->_endpointPaths)) {
            return false;
        }

        $fileName = preg_replace(
            '/^(.*?)endpoint$/sui',
            '$1Endpoint.php',
            $className
        );

        foreach ($this->_endpointPaths as $endpointDirPath) {
            $filePath = sprintf('%s/%s', $endpointDirPath, $fileName);

            if (file_exists($filePath) && is_file($filePath)) {
                require_once $filePath;

                break;
            }
        }

        return true;
    }

    /**
     * @param string|null $className
     * @return bool
     */
    private function _loadMiddleware(?string $className = null): bool
    {
        if (empty($className) || empty($this->_middlewarePaths)) {
            return false;
        }

        $fileName = preg_replace(
            '/^(.*?)middleware$/sui',
            '$1Middleware.php',
            $className
        );

        foreach ($this->_middlewarePaths as $middlewareDirPath) {
            $filePath = sprintf('%s/%s', $middlewareDirPath, $fileName);

            if (file_exists($filePath) && is_file($filePath)) {
                require_once $filePath;

                break;
            }
        }

        return true;
    }

    /**
     * @param string|null $className
     * @return bool
     */
    private function _loadController(?string $className = null): bool
    {
        if (empty($className) || empty($this->_controllerPaths)) {
            return false;
        }

        $fileName = preg_replace(
            '/^(.*?)controller$/sui',
            '$1Controller.php',
            $className
        );

        foreach ($this->_controllerPaths as $controllerDirPath) {
            $filePath = sprintf('%s/%s', $controllerDirPath, $fileName);

            if (file_exists($filePath) && is_file($filePath)) {
                require_once $filePath;

                break;
            }
        }

        return true;
    }
}
