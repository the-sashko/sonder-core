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
     * @var object Model Object Class Instance
     */
    public $object = null;

    /**
     * @var object Model API Class Instance
     */
    public $api = null;

    /**
     * @var string Model Values Object Class Name
     */
    public $voClassName = null;

    /**
     * @var array Data From JSON Config Files
     */
    public $configData = [];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Set Model Object Class Instance
     *
     * @param string|null $objectClassName Model Object Class Name
     */
    public function setObject(?string $objectClassName = null): void
    {
        if (empty($objectClassName)) {
            throw new Exception('Model`s Object Class Name Is Not Set!');
        }

        if (null === $this->object) {
            $dbConfig = $this->getConfig('database');
            $this->object = new $objectClassName($dbConfig);
        }
    }

    /**
     * Set Model Value Object Class Name
     *
     * @param string|null $voClassName Model Value Object Class Name
     */
    public function setValuesObjectClass(?string $voClassName = null): void
    {
        if (empty($voClassName)) {
            throw new Exception(
                'Model`s Values Object Class Name Is Not Set!'
            );
        }

        $this->voClassName = $voClassName;
    }

    /**
     * Set Model API Class Name
     *
     * @param string|null $apiClassName Model Api Class Name
     */
    public function setApi(?string $apiClassName = null): void
    {
        if (empty($apiClassName)) {
            throw new Exception(
                'Model`s API Class Name Is Not Set!'
            );
        }

        $this->api = new $apiClassName();
    }

    /**
     * Get Model Value Object Instance
     *
     * @param array|null $row List Of Values
     *
     * @return ValuesObject|null Values Object Instance
     */
    public function getVO(?array $row = null): ?ValuesObject
    {
        if (null === $this->voClassName) {
            throw new Exception('Value Object class not set');
        }

        return new $this->voClassName($row);
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
}
