<?php

namespace Sonder\Core;

final class AutoloadCore
{
    /**
     * @var array
     */
    private array $_endpointPaths = [];

    /**
     * @var array
     */
    private array $_middlewarePaths = [];

    /**
     * @var array
     */
    private array $_controllerPaths = [];

    final public function __construct()
    {
        $this->_endpointPaths = APP_SOURCE_PATHS['endpoints'];
        $this->_middlewarePaths = APP_SOURCE_PATHS['middlewares'];
        $this->_controllerPaths = APP_SOURCE_PATHS['controllers'];
    }

    /**
     * @param string|null $className
     *
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

        $className = mb_convert_case($className, MB_CASE_LOWER);
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
     *
     * @return bool
     */
    private function _loadEndpoint(?string $className = null): bool
    {
        if (empty($className) || empty($this->_endpointPaths)) {
            return false;
        }

        $endpointName = preg_replace(
            '/^(.*?)endpoint$/su',
            '$1',
            $className
        );

        $endpointName = mb_convert_case($endpointName, MB_CASE_TITLE);
        $fileName = sprintf('%sEndpoint.php', $endpointName);

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
     *
     * @return bool
     */
    private function _loadMiddleware(?string $className = null): bool
    {
        if (empty($className) || empty($this->_middlewarePaths)) {
            return false;
        }

        $middlewareName = preg_replace(
            '/^(.*?)middleware$/su',
            '$1',
            $className
        );

        $middlewareName = mb_convert_case($middlewareName, MB_CASE_TITLE);
        $fileName = sprintf('%sMiddleware.php', $middlewareName);

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
     *
     * @return bool
     */
    private function _loadController(?string $className = null): bool
    {
        if (empty($className) || empty($this->_controllerPaths)) {
            return false;
        }

        $controllerName = preg_replace(
            '/^(.*?)controller$/su',
            '$1',
            $className
        );

        $controllerName = mb_convert_case($controllerName, MB_CASE_TITLE);
        $fileName = sprintf('%sController.php', $controllerName);

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