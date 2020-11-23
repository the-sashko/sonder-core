<?php
/**
 * Core Model Class
 */
class ModelCore extends CommonCore
{
    /**
     * @var string Path To Main Config File
     */
    const MAIN_CONFIG_PATH = __DIR__.'/../../config/main.json';

    /**
     * @var string Path To Exceptions Directory
     * */
    const EXCEPTIONS_DIR_PATH = __DIR__.'/../../exceptions';

    /**
     * @var object Model Object Class Instance
     */
    public $object = null;

    /**
     * @var object Model API Class Instance
     */
    public $api = null;

    /**
     * @var array Data From JSON Config Files
     */
    public $configData = [];

    /**
     * @var string Model Values Object Class Name
     */
    public $valuesObjectClass = null;

    public function __construct()
    {
        parent::__construct();

        $this->_includeException();
        $this->_includeForm();

        $this->_setObject();
        $this->_setValuesObject();
        $this->_setApi();
    }

    /**
     * Get Model Value Object Instance
     *
     * @param array|null $row List Of Values
     *
     * @return ValuesObject Values Object Instance
     */
    public function getVO(?array $row = null): ValuesObject
    {
        if (null === $this->valuesObjectClass) {
            throw new Exception('Value Object class not set');
        }

        return new $this->valuesObjectClass($row);
    }

    /**
     * Get List Of Model Value Object Instances
     *
     * @param array|null $rows List Of Values
     *
     * @return array|null List Of Model Value Object Instances
     */
    public function getVOArray(?array $rows = null): ?array
    {
        $voArray = [];

        if (null === $rows) {
            return null;
        }

        foreach ($rows as $row) {
            $valuesObject = $this->getVO($row);

            if (!empty($valuesObject)) {
                $voArray[] = $valuesObject;
            }
        }

        return $voArray;
    }

    /**
     * Include Model Exception
     */
    private function _includeException(): void
    {
        $exceptionFilePath = sprintf(
            '%s/%sException.php',
            static::EXCEPTIONS_DIR_PATH,
            __CLASS__
        );

        if (file_exists($exceptionFilePath) && is_file($exceptionFilePath)) {
            require_once($exceptionFilePath);
        }
    }

    /**
     * Include Model Form
     */
    private function _includeForm(): void
    {
        $modelName    = mb_convert_case(__CLASS__, MB_CASE_LOWER);
        $formFilePath = sprintf('%s/%s.form.php', __DIR__, $modelName);

        if (file_exists($formFilePath) && is_file($formFilePath)) {
            require_once($formFilePath);
        }
    }

    /**
     * Set Model Object Class Instance
     */
    private function _setObject(): void
    {
        $modelName     = mb_convert_case(__CLASS__, MB_CASE_LOWER);
        $objectFilePath = sprintf('%s/%s.object.php', __DIR__, $modelName);
        $objectClass    = sprintf('%sObject', __CLASS__);

        if (file_exists($objectFilePath) && is_file($objectFilePath)) {
            require_once($objectFilePath);

            $databaseConfig = $this->getConfig('database');

            $this->object = new $objectClass($databaseConfig);
        }
    }

    /**
     * Set Model Value Object Class Instance
     */
    private function _setValuesObject(): void
    {
        $modelName            = mb_convert_case(__CLASS__, MB_CASE_LOWER);
        $valuesObjectFilePath = sprintf('%s/%s.vo.php', __DIR__, $modelName);
        $valuesObjectClass    = sprintf('%sValuesObject', __CLASS__);

        if (
            file_exists($valuesObjectFilePath) &&
            is_file($valuesObjectFilePath)
        ) {
            require_once($valuesObjectFilePath);

            $this->valuesObjectClass = $valuesObjectClass;
        }
    }

    /**
     * Set Model API Class Name
     */
    private function _setApi(): void
    {
        $modelName   = mb_convert_case(__CLASS__, MB_CASE_LOWER);
        $apiFilePath = sprintf('%s/%s.api.php', __DIR__, $modelName);
        $apiClass    = sprintf('%sApi', __CLASS__);

        if (file_exists($apiFilePath) && is_file($apiFilePath)) {
            require_once($apiFilePath);

            $this->api = new $apiClass();
        }
    }
}
