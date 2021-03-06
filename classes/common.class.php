<?php
/**
 * Parent Class For All Controllers And Models
 */
class CommonCore
{
    /**
     * @var string Default Language Of Application
     */
    const DEFAULT_LANGUAGE = 'en';

    /**
     * @var string Path To Hooks Directory
     * */
    const HOOKS_DIR_PATH = __DIR__.'/../../hooks/';

    /**
     * @var string Path To Models Directory
     * */
    const MODELS_DIR_PATH = __DIR__.'/../../models';

    /**
     * @var object|null Session Plugin Instance
     * */
    public $session = null;

    /**
     * @var array Data From JSON Config Files
     */
    public $configData = [];

    /**
     * @var string|null Current URL
     */
    public $currentUrl = null;

    /**
     * @var string|null Current Host
     */
    public $currentHost = null;

    /**
     * @var string|null Current Language Of User
     */
    public $language = null;

    public function __construct()
    {
        $this->_requireAppException();

        $this->_setConfigs();

        $this->session = $this->getPlugin('session');

        $this->_setCurrentUrl();
        $this->_setCurrentHost();
    }

    /**
    * Get Model By Name
    *
    * @param string|null $modelName Name Of Model
    *
    * @return ModelCore|null Insnace Of Model
    */
    public function getModel(?string $modelName = null): ?ModelCore
    {
        if (empty($modelName)) {
            return null;
        }

        $modelDirPath  = sprintf('%s/%s', static::MODELS_DIR_PATH, $modelName);
        $modelFilePath = sprintf('%s/%s.php', $modelDirPath, $modelName);
        $modelClass    = mb_convert_case($modelName, MB_CASE_TITLE);

        if (!file_exists($modelFilePath) ||!is_file($modelFilePath)) {
            $errorMessage = sprintf(
                '%s. Model: %s',
                CoreException::MESSAGE_CORE_MODEL_NOT_FOUND,
                $modelName
            );

            throw new CoreException(
                $errorMessage,
                CoreException::CODE_CORE_MODEL_NOT_FOUND
            );
        }

        require_once($modelFilePath);

        if (!class_exists($modelClass)) {
            $errorMessage = sprintf(
                '%s. Model: %s',
                CoreException::MESSAGE_CORE_MODEL_NOT_FOUND,
                $modelName
            );

            throw new CoreException(
                $errorMessage,
                CoreException::CODE_CORE_MODEL_NOT_FOUND
            );
        }

        return new $modelClass();
    }

    /**
     * Get Plugin Instance By Name
     *
     * @param string|null $pluginName Name Of Plugin
     *
     * @return Object Insnace Of Plugin
     */
    public function getPlugin(?string $pluginName = null): Object
    {
        if (empty($pluginName)) {
            throw new CoreException(
                CoreException::MESSAGE_CORE_PLUGIN_IS_NOT_SET,
                CoreException::CODE_CORE_PLUGIN_IS_NOT_SET
            );
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
     * Set Laguage Value To Session
     */
    public function setLanguage(?string $language = null): void
    {
        $defaultLanguage = static::DEFAULT_LANGUAGE;

        if (defined('DEFAULT_LANGUAGE')) {
            $defaultLanguage = DEFAULT_LANGUAGE;
        }

        if (empty($language)) {
            $language = $defaultLanguage;
        }

        $this->session->set('language', $language);
        $this->language = $language;
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
     * Redirect To URL
     *
     * @param string|null $url         URL Value
     * @param bool        $isPermanent Is Redirect Permanently
     */
    public function redirect(
        ?string $url         = null,
        bool    $isPermanent = false
    ): void
    {
        $url  = empty($url) ? '/' : $url;
        $code = $isPermanent ? 301 : 302;

        header(sprintf('Location: %s', $url), true, $code);

        exit(0);
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

    /**
     * Set Current URL
     */
    private function _setCurrentUrl(): void
    {
        $this->currentUrl = '/';

        if (
            array_key_exists('REAL_REQUEST_URI', $_SERVER) &&
            !empty($_SERVER['REAL_REQUEST_URI'])
        ) {
            $this->currentUrl = $_SERVER['REAL_REQUEST_URI'];
        }
    }

    /**
     * Set Current Host
     */
    private function _setCurrentHost(): void
    {
        $mainConfigData = $this->configData['main'];

        if (
            !array_key_exists('site_protocol', $mainConfigData) ||
            !array_key_exists('site_domain', $mainConfigData)
        ) {
            throw new \Exception('Main Config Has Bad Format');
        }

        $this->currentHost = sprintf(
            '%s://%s',
            (string) $mainConfigData['site_protocol'],
            (string) $mainConfigData['site_domain']
        );
    }

    /**
     * Execute Hook
     *
     * @param string $hookName   Name Of Hook
     * @param string $hookMethod Method Of Hook
     * @param array  $entityData Entity Data
     */
    public function _execHook(
        string $hookName,
        string $hookMethod,
        array  &$entityData
    ): void
    {
        $hookClass = mb_convert_case($hookName, MB_CASE_TITLE);
        $hookClass = sprintf('%sHook', $hookClass);

        $hookFile = '%s/%s.php';

        $hookFile = sprintf(
            $hookFile,
            static::HOOKS_DIR_PATH,
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
        $this->configData['main']     = $this->getConfig('main');
        $this->configData['database'] = $this->getConfig('database');
        $this->configData['hooks']    = $this->getConfig('hooks');
        $this->configData['seo']      = $this->getConfig('seo');
    }
}
