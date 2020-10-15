<?php
/**
 * Parent Class For All Controllers And Models
 */
class CommonCore
{
    /**
     * @var object Session Plugin Instance
     * */
    public $session = null;

    /**
     * @var array Data From JSON Config Files
     */
    public $configData = [];

    /**
     * @var object ServerInfo Plugin Instance
     * */
    public $serverInfo = null;

    public function __construct()
    {
        $this->session    = $this->getPlugin('session');
        $this->serverInfo = $this->getPlugin('serverInfo');
    }

    /**
    * Get Model Instance By Name
    *
    * @param string|null $model Name Of Model
    *
    * @return ModelCore|null Insnace Of Model
    */
    public function getModel(?string $model = null): ?ModelCore
    {
        if (empty($model)) {
            return null;
        }

        $modelsDir  = __DIR__.'/../../models';
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

        if (file_exists("{$modelsDir}/{$model}/{$model}.api.php")) {
            require_once("{$modelsDir}/{$model}/{$model}.api.php");
            $modelInstance->setApi("{$modelClass}Api");
        }

        return $modelInstance;
    }

    /**
    * Get Plugin Instance By Name
    *
    * @param string|null $pluginName Name Of Plugin
    *
    * @return Object|null Insnace Of Plugin
    */
    public function getPlugin(?string $pluginName = null): ?Object
    {
        if (empty($pluginName)) {
            return null;
        }

        $pluginClass = sprintf('%sPlugin', $pluginName);

        if (!class_exists($pluginClass)) {
            $errorMessage = sprintf('Plugin %s Is Not Exists!', $pluginName);
            throw new Exception($errorMessage);
        }

        return new $pluginClass();
    }

    /**
    * Get Config Data By Config File Name
    *
    * @param string|null $configName Name Of Config File
    *
    * @return array|null Data From Config File
    */
    public function getConfig(?string $configName = null): ?array
    {
        if (empty($configName)) {
            return null;
        }

        $configPath = __DIR__."/../../config/{$configName}.json";

        if (!file_exists($configPath)) {
            throw new Exception("Config File {$configName} Missing");
        }

        $configJSON = file_get_contents($configPath);

        return (array) json_decode($configJSON, true);
    }

    /**
     * Create Log
     *
     * @param string $errMessage Message
     */
    public function log(string $message): void
    {
        $logName = explode('\\', static::class);
        $logName = (string) end($logName);

        $this->getPlugin('logger')->log($message, $logName, APP_MODE);
    }

    /**
     * Create Error Log
     *
     * @param string $errMessage Error Message
     */
    public function logError(string $message): void
    {
        $logName = explode('\\', static::class);
        $logName = (string) end($logName);

        $this->getPlugin('logger')->logError($message, $logName);
    }
}
