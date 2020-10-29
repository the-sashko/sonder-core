<?php
/**
 * Parent Class For All Controllers And Models
 */
class CommonCore
{
    /**
     * @var string Path To Hooks Directory
     * */
    const HOOKS_DIR_PATH = __DIR__.'/../../hooks/';

    /**
     * @var object|null Session Plugin Instance
     * */
    public $session = null;

    /**
     * @var array Data From JSON Config Files
     */
    public $configData = [];

    public function __construct()
    {
        $this->_requireAppException();

        $this->_setConfigs();

        $this->session = $this->getPlugin('session');
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
        $modelsDir  = sprintf('%s/%s', $modelsDir, $model);
        $modelClass = mb_convert_case($model, MB_CASE_TITLE);

        $exceptionsDir  = __DIR__.'/../../exceptions';
        $exceptionClass = mb_convert_case($model, MB_CASE_TITLE);
        $exceptionClass = sprintf('%sException', $exceptionClass);

        $modelFilePath = sprintf('%s/%s.php', $modelsDir, $model);
        $modelFormFilePath = sprintf('%s/%s.form.php', $modelsDir, $model);
        $modelObjectFilePath = sprintf('%s/%s.object.php', $modelsDir, $model);
        $modelApiFilePath = sprintf('%s/%s.api.php', $modelsDir, $model);

        $modelValuesObjectFilePath = sprintf(
            '%s/%s.vo.php',
            $modelsDir,
            $model
        );

        $exceptionFilePath = sprintf(
            '%s/%s.php',
            $exceptionsDir,
            $exceptionClass
        );

        if (!file_exists($modelFilePath) ||!is_file($modelFilePath)) {
            $errorMessage = CoreException::MESSAGE_CORE_MODEL_NOT_FOUND;
            $errorMessage = sprintf('%s. Model: %s', $errorMessage, $model);

            throw new CoreException(
                $errorMessage,
                CoreException::CODE_CORE_MODEL_NOT_FOUND
            );
        }

        if (file_exists($modelFormFilePath) && is_file($modelFormFilePath)) {
            require_once($modelFormFilePath);
        }

        require_once($modelFilePath);

        $modelInstance = new $modelClass();

        if (
            file_exists($modelObjectFilePath) &&
            is_file($modelObjectFilePath)
        ) {
            require_once($modelObjectFilePath);
            $modelInstance->setObject(sprintf('%sObject', $modelClass));
        }

        if (file_exists($modelApiFilePath) && is_file($modelApiFilePath)) {
            require_once($modelApiFilePath);
            $modelInstance->setApi(sprintf('%sApi', $modelClass));
        }

        if (
            file_exists($modelValuesObjectFilePath) &&
            is_file($modelValuesObjectFilePath)
        ) {
            require_once($modelValuesObjectFilePath);
            $modelValuesObjectClass = sprintf('%sValuesObject', $modelClass);
            $modelInstance->setValuesObjectClass($modelValuesObjectClass);
        }

        if (file_exists($exceptionFilePath) && is_file($exceptionFilePath)) {
            require_once($exceptionFilePath);
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
            $errorMessage = CoreException::MESSAGE_CORE_PLUGIN_IS_NOT_EXISTS;

            $errorMessage = sprintf(
                '%s. Plugin: %s Is Not Exists!',
                $errorMessage,
                $pluginName
            );

            throw new CoreException(
                $errorMessage,
                CoreException::CODE_CORE_PLUGIN_IS_NOT_EXISTS
            );
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
            $errorMessage = CoreException::MESSAGE_CORE_CONFIG_IS_NOT_EXISTS;

            $errorMessage = sprintf(
                '%s. Config: %s',
                $errorMessage,
                $configName
            );

            throw new CoreException(
                $errorMessage,
                CoreException::CODE_CORE_CONFIG_IS_NOT_EXISTS
            );
        }

        $configJSON = file_get_contents($configPath);

        return (array) json_decode($configJSON, true);
    }

    /**
     * Execute All Hooks In Scope
     *
     * @param string|null $hookScope  Scope Of Hooks
     * @param array|null  $entityData Entity Data
     *
     * @return bool Is Hooks Successfully Execured
     */
    public function execHooks(
        ?string $hookScope   = null,
        ?array  &$entityData = null
    ): bool
    {
        $hooksData = $this->configData['hooks'];

        $entityData = empty($entityData) ? [] : $entityData;

        if (!array_key_exists($hookScope, $hooksData)) {
            return false;
        }

        foreach ($hooksData[$hookScope] as $hookItem) {
            if (
                !array_key_exists('hook', $hookItem) ||
                empty($hookItem['hook'])
            ) {
                continue;
            }

            if (
                !array_key_exists('method', $hookItem) ||
                empty($hookItem['method'])
            ) {
                continue;
            }

            $hookName   = $hookItem['hook'];
            $hookMethod = $hookItem['method'];

            $this->_execHook($hookName, $hookMethod, $entityData);
        }

        return true;
    }

    /**
     * Execute Hook
     *
     * @param string|null $hookName   Name Of Hook
     * @param string|null $hookMethod Method Of Hook
     * @param array|null  $entityData Entity Data
     */
    public function _execHook(
        string $hookName,
        string $hookMethod,
        array  &$entityData
    ): void
    {
        $hookClass = mb_convert_case($hookName, MB_CASE_TITLE);
        $hookClass = sprintf('%sHook', $hookClass);

        $hookFile = '%s/%s/%s.php';

        $hookFile = sprintf(
            $hookFile,
            static::HOOKS_DIR_PATH,
            $hookName,
            $hookName
        );

        $hookAutoloadFile = '%s/%s/autoload.php';

        $hookAutoloadFile = sprintf(
            $hookAutoloadFile,
            static::HOOKS_DIR_PATH,
            $hookName
        );

        if (file_exists($hookAutoloadFile) && is_file($hookAutoloadFile)) {
            $hookFile = $hookAutoloadFile;
        }

        if (!file_exists($hookFile) || !is_file($hookFile)) {
            $errorMessage = sprintf(
                '%s. Hook: %s',
                CoreException::MESSAGE_CORE_HOOK_IS_NOT_EXISTS,
                $hookName
            );

            throw new CoreException(
                $errorMessage,
                CoreException::CODE_CORE_HOOK_IS_NOT_EXISTS
            );
        }

        require_once $hookFile;

        if (!class_exists($hookClass)) {
            $errorMessage = sprintf(
                '%s. Class: %s',
                CoreException::MESSAGE_CORE_HOOK_CLASS_IS_NOT_EXISTS,
                $hookClass
            );

            throw new CoreException(
                $errorMessage,
                CoreException::CODE_CORE_HOOK_CLASS_IS_NOT_EXISTS
            );
        }

        $hookInstance = new $hookClass($entityData);

        if (!method_exists($hookInstance, $hookMethod)) {
            $errorMessage = sprintf(
                '%s. Class: %s. Method: %s',
                CoreException::MESSAGE_CORE_HOOK_METHOD_IS_NOT_EXISTS,
                $hookClass,
                $hookMethod
            );

            throw new CoreException(
                $errorMessage,
                CoreException::CODE_CORE_HOOK_METHOD_IS_NOT_EXISTS
            );
        }

        $hookInstance->$hookMethod();

        $entityData = $hookInstance->getEntity();
    }

    /**
     * Set Data From JSON Config Files
     */
    private function _setConfigs(): void
    {
        $this->configData['main']  = $this->getConfig('main');
        $this->configData['hooks'] = $this->getConfig('hooks');
        $this->configData['seo']   = $this->getConfig('seo');
    }

    /**
     * Create Log
     *
     * @param string $message Message
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
     * @param string $message Error Message
     */
    public function logError(string $message): void
    {
        $logName = explode('\\', static::class);
        $logName = (string) end($logName);

        $this->getPlugin('logger')->logError($message, $logName);
    }

    /**
     * Require AppException Class If It Exists And Not Included
     */
    private function _requireAppException(): void
    {
        if (
            !defined('AppException') &&
            file_exists(__DIR__.'/../../exceptions/AppException.php') &&
            is_file(__DIR__.'/../../exceptions/AppException.php')
        ) {
            require_once __DIR__.'/../../exceptions/AppException.php';
        }
    }
}
