<?php
/**
 * Core Model Class
 */
class ModelCore extends CommonCore
{
    const MAIN_CONFIG_PATH = __DIR__.'/../../config/main.json';

    public $object        = null;
    public $voClassName   = null;
    public $form          = null;
    public $configData    = [];

    /**
     * summary
     */
    public function __construct()
    {
        $this->setConfigData();
    }

    /**
     * summary
     */
    public function setObject(string $objectClassName = '') : void
    {
        if ($this->object === null) {
            $this->object = new $objectClassName();
            $this->object->initStore();
        }
    }

    /**
     * summary
     */
    public function setValuesObjectClass(string $voClassName = '') : void
    {
        $this->voClassName = $voClassName;
    }

    /**
     * summary
     */
    public function setConfigData() : void
    {
        $configDataJSON = file_get_contents(self::MAIN_CONFIG_PATH);
        $this->configData = json_decode($configDataJSON, true);
    }

    /**
     * summary
     */
    public function getConfigValue(string $valueName = '') : string
    {
        if (isset($this->configData[$valueName])) {
            return (string) $this->configData[$valueName];
        }

        return NULL;
    }

    /**
     * summary
     */
    public function getConfigArrayValue(string $valueName = '') : array
    {
        if (isset($this->configData[$valueName])) {
            return (array) $this->configData[$valueName];
        }

        return [];
    }

    /**
     * summary
     */
    public function getVO(array $inputArray = []) : ValuesObject
    {
        if ($this->voClassName == NULL) {
            throw new Exception('Value Object class not set');
        }

        return new $this->voClassName($inputArray);
    }

    /**
     * summary
     */
    public function getVOArray(array $inputArrays = []) : array
    {
        foreach ($inputArrays as $inputArraysIDX => $inputArray) {
            $inputArrays[$inputArraysIDX] = $this->getVO($inputArray);
        }

        return $inputArrays;
    }

    public function getByID(int $id = -1) : ValuesObject
    {
        $values = $this->object->getByID(
            $this->object->getDefaultTableName(),
            $id
        );

        return $this->getVO($values);
    }

    public function getByPage(int $page = 1) : array
    {
        $values = $this->object->getAllByPage(
            $this->object->getDefaultTableName(),
            [],
            $page
        );

        return $this->getVOArray($values);
    }

    public function removeByID(int $id = -1) : bool
    {
        return $this->object->removeByID(
            $this->object->getDefaultTableName(),
            $id
        );
    }
}
?>