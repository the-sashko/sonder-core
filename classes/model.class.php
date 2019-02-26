<?php
/**
 * Core Model Class
 */
class ModelCore extends CommonCore
{
    const MAIN_CONFIG_PATH = __DIR__.'/../../config/main.json';

    public $object        = null;
    public $voClassName   = null;
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

    /**
     * summary
     */
    public function getSlug(string $input = '', int $id = -1) : string
    {
        $translit  = $this->initLib('translit');
        $slug = $translit->getSlug($input);
        return $this->_getUniqSlug($slug, $id);
    }

    /**
     * summary
     */
    protected function _getUniqSlug(string $slug = '', int $id = -1) : string
    {
        $condition = "\"slug\" = '{$slug}' AND \"id\" != {$id}";

        $value = $this->object->getOneByCondition(
            $this->object->getDefaultTableName(),
            [],
            $condition
        );

        if (!count($modelValue) > 0) {
            return $slug;
        }

        if (preg_match('/^(.*?)\-([0-9]+)$/su', $slug)) {
            $slugNumber = (int) preg_replace(
                '/^(.*?)\-([0-9]+)/su',
                '$2',
                $slug
            );
            $slugNumber++;
            $slug = preg_replace('/^(.*?)\-([0-9]+)/su', '$1', $slug);
            $slug = $slug.'-'.$slugNumber;
        } else {
            $slug = $slug.'-1';
        }

        return $this->_getUniqSlug($slug, $id);
    }
}
?>