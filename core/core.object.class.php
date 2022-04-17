<?php

namespace Sonder\Core;

use Exception;
use Sonder\Core\Interfaces\IModel;
use Sonder\Exceptions\AppException;
use Sonder\Exceptions\CoreException;

class CoreObject
{
    /**
     * @var ConfigObject
     */
    protected ConfigObject $config;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->config = new ConfigObject();
    }

    /**
     * @param string $pluginName
     * @param mixed ...$pluginValues
     * @return object
     * @throws Exception
     */
    final public static function getPlugin(
        string $pluginName,
        mixed  ...$pluginValues
    ): object
    {
        $pluginName = mb_convert_case($pluginName, MB_CASE_LOWER);

        $pluginClassName = sprintf(
            '\Sonder\Plugins\%sPlugin',
            mb_convert_case($pluginName, MB_CASE_TITLE)
        );

        if (class_exists($pluginClassName, false)) {
            return new $pluginClassName($pluginValues);
        }

        $pluginFileName = sprintf(
            '%sPlugin.php',
            mb_convert_case($pluginName, MB_CASE_TITLE)
        );

        $pluginsPaths = [
            APP_PROTECTED_DIR_PATH . '/plugins',
            APP_FRAMEWORK_DIR_PATH . '/plugins'
        ];

        if (
            array_key_exists('plugins', APP_SOURCE_PATHS) &&
            is_array(APP_SOURCE_PATHS['plugins'])
        ) {
            $pluginsPaths = APP_SOURCE_PATHS['plugins'];
        }

        foreach ($pluginsPaths as $pluginDirPath) {
            $pluginFilePath = sprintf(
                '%s/%s/%s',
                $pluginDirPath,
                $pluginName,
                $pluginFileName
            );

            $pluginInitFilePath = sprintf(
                '%s/%s/init.php',
                $pluginDirPath,
                $pluginName
            );

            if (
                file_exists($pluginInitFilePath) &&
                is_file($pluginInitFilePath)
            ) {
                require_once $pluginInitFilePath;

                break;
            }

            if (file_exists($pluginFilePath) && is_file($pluginFilePath)) {
                require_once $pluginFilePath;

                break;
            }
        }

        if (!class_exists($pluginClassName, false)) {
            $errorMessage = sprintf(
                CoreException::MESSAGE_CORE_PLUGIN_NOT_EXISTS,
                $pluginName
            );

            throw new CoreException(
                $errorMessage,
                AppException::CODE_CORE_PLUGIN_NOT_EXISTS
            );
        }

        if (empty($pluginValues)) {
            return new $pluginClassName();
        }

        return new $pluginClassName(...$pluginValues);
    }

    /**
     * @param string $modelName
     * @return IModel
     * @throws Exception
     */
    final protected function getModel(string $modelName): IModel
    {
        $modelName = mb_convert_case($modelName, MB_CASE_LOWER);

        $modelClassName = sprintf(
            '\Sonder\Models\%s',
            mb_convert_case($modelName, MB_CASE_TITLE)
        );

        if (class_exists($modelClassName, false)) {
            return new $modelClassName();
        }

        $modelFileName = sprintf(
            '%s.php',
            mb_convert_case($modelName, MB_CASE_TITLE)
        );

        $modelsPaths = [
            APP_PROTECTED_DIR_PATH . '/models',
            APP_FRAMEWORK_DIR_PATH . '/models'
        ];

        if (
            array_key_exists('models', APP_SOURCE_PATHS) &&
            is_array(APP_SOURCE_PATHS['models'])
        ) {
            $modelsPaths = APP_SOURCE_PATHS['models'];
        }

        foreach ($modelsPaths as $modelDirPath) {
            $modelDirPath = sprintf('%s/%s', $modelDirPath, $modelName);

            $modelFilePath = sprintf(
                '%s/%s',
                $modelDirPath,
                $modelFileName
            );

            $modelInitFilePath = sprintf('%s/init.php', $modelDirPath);

            if (file_exists($modelFilePath) && is_file($modelFilePath)) {
                if (
                    file_exists($modelInitFilePath) &&
                    is_file($modelInitFilePath)
                ) {
                    require_once $modelInitFilePath;
                }

                foreach (glob($modelDirPath . '/*.php') as $filePath) {
                    if (is_file($filePath)) {
                        require_once $filePath;
                    }
                }

                break;
            }
        }

        if (!class_exists($modelClassName, false)) {
            $errorMessage = sprintf(
                CoreException::MESSAGE_CORE_MODEL_NOT_EXISTS,
                $modelName
            );

            throw new CoreException(
                $errorMessage,
                AppException::CODE_CORE_MODEL_NOT_EXISTS
            );
        }

        return new $modelClassName();
    }
}
