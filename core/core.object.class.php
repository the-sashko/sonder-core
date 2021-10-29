<?php

namespace Sonder\Core;

use Exception;
use Sonder\Core\Interfaces\IModel;

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
     *
     * @return object
     *
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
            throw new Exception(
                sprintf('Plugin %s is not exist', $pluginName)
            );
        }

        if (empty($pluginValues)) {
            return new $pluginClassName();
        }

        return new $pluginClassName(...$pluginValues);
    }

    /**
     * @param string $modelName
     *
     * @return IModel
     *
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
            '%sModel.php',
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

            if (file_exists($modelFilePath) && is_file($modelFilePath)) {
                foreach (glob($modelDirPath . '/*.php') as $filePath) {
                    if (is_file($filePath)) {
                        require_once $filePath;
                    }
                }

                break;
            }
        }

        if (!class_exists($modelClassName, false)) {
            throw new Exception(
                sprintf('Model %s is not exist', $modelName)
            );
        }

        return new $modelClassName();
    }
}
