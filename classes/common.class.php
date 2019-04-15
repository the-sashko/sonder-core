<?php
/**
 * Parent Class For All Controllers And Models
 */
class CommonCore
{
    /**
    * Get Model Instance By Name
    *
    * @param string $model Name Of Model
    *
    * @return Object ModelCore Insnace Of Model
    */
    public function initModel(string $model = '') : ModelCore
    {
        $modelsDir = __DIR__.'/../../models';
        $modelClass = mb_convert_case($model, MB_CASE_TITLE);

        if (!file_exists("{$modelsDir}/{$model}/{$model}.php")) {
            throw new Exception("Missing {$modelClass} Model!");
        }

        if (file_exists("{$modelsDir}/{$model}/{$model}.form.php")) {
            require_once("{$modelsDir}/{$model}/{$model}.form.php");
        }

        require_once("{$modelsDir}/{$model}/{$model}.php");
        $modelInstance = new $modelClass();

        if (file_exists("{$modelsDir}/{$model}/{$model}.object.php")) {
            require_once("{$modelsDir}/{$model}/{$model}.object.php");
            $modelInstance->setObject("{$modelClass}Object");
        }

        if (file_exists("{$modelsDir}/{$model}/{$model}.vo.php")) {
            require_once("{$modelsDir}/{$model}/{$model}.vo.php");
            $modelInstance->setValuesObjectClass("{$modelClass}VO");
        }

        return $modelInstance;
    }

    /**
    * Get Plugin Instance By Name
    *
    * @param string $plugin Name Of Plugin
    *
    * @return Object Insnace Of Plugin
    */
    public function initPlugin(string $plugin = '') : Object
    {
        $pluginClass = "{$plugin}Plugin";

        if (!class_exists($pluginClass)) {
            throw new Exception("Plugin {$plugin} Is Missing!");
        }

        return new $pluginClass();
    }

    /**
    * Get Config Data By Config File Name
    *
    * @param string $configName Name Of Config File
    *
    * @return array Data From Config File
    */
    public function initConfig(string $configName = '') : array
    {
        $configPath = __DIR__."/../../config/{$configName}.json";

        if (!file_exists($configPath)) {
            throw new Exception("Config File {$configName} Missing");
        }

        $configJSON = file_get_contents($configPath);

        return (array) json_decode($configJSON, true);
    }
}
?>