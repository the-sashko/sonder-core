<?php

namespace Sonder\Core;

use Exception;
use Sonder\Core\Interfaces\IHook;
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

        foreach (APP_SOURCE_PATHS['plugins'] as $pluginDirPath) {
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

    final protected function runHooks(): void
    {
        //TODO
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

        foreach (APP_SOURCE_PATHS['models'] as $modelDirPath) {
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

    /**
     * @param string $hookName
     * @param mixed ...$hookValues
     *
     * @return IHook
     *
     * @throws Exception
     */
    private function _getHook(
        string $hookName,
        mixed  ...$hookValues
    ): IHook
    {
        $hookName = mb_convert_case($hookName, MB_CASE_TITLE);

        $hookClassName = sprintf('\Sonder\Hooks\%sHook', $hookName);

        if (class_exists($hookClassName, false)) {
            return new $hookClassName($hookValues);
        }

        $hookFileName = sprintf('%sHook.php', $hookName);

        foreach (APP_SOURCE_PATHS['hooks'] as $hookDirPath) {
            $hookFilePath = sprintf(
                '%s/%s',
                $hookDirPath,
                $hookFileName
            );

            if (file_exists($hookFilePath) && is_file($hookFilePath)) {
                require_once $hookFilePath;

                break;
            }
        }

        if (!class_exists($hookClassName, false)) {
            throw new Exception(
                sprintf('Hook %s is not exist', $hookName)
            );
        }

        return new $hookClassName($hookValues);
    }
}
