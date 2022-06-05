<?php

namespace Sonder\Core;

use Exception;
use Sonder\Core\Interfaces\IModel;

class CoreModel extends CoreObject implements IModel
{
    /**
     * @var object|null
     */
    public ?object $api = null;

    /**
     * @var object|null
     */
    protected ?object $store = null;

    /**
     * @var int
     */
    protected int $itemsOnPage = 10;

    /**
     * @var string|null
     */
    private ?string $_valuesObjectClass = null;

    /**
     * @var string|null
     */
    private ?string $_simpleValuesObjectClass = null;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();

        $this->_setStore();
        $this->_setValuesObjectClasses();
        $this->_setApi();
    }

    /**
     * @param array|null $formValues
     * @param string|null $formName
     * @return ModelFormObject|null
     */
    final public function getForm(
        ?array  $formValues = null,
        ?string $formName = null
    ): ?ModelFormObject
    {
        $modelClass = get_called_class();

        $modelName = explode('\\', $modelClass);
        $modelName = end($modelName);

        $formName = empty($formName) ? $modelName : $formName;

        $formName = (string)$formName;
        $formName = ucwords($formName, '_');
        $formName = ucfirst($formName);
        $formName = explode('_', $formName);
        $formName = implode('', $formName);

        $formClass = sprintf(
            '%s\\%sForm',
            $modelClass,
            $formName
        );

        if (!class_exists($formClass)) {
            return null;
        }

        return new $formClass($formValues);
    }

    /**
     * @param array|null $row
     * @return ValuesObject
     * @throws Exception
     */
    protected function getVO(?array $row = null): ValuesObject
    {
        if (empty($this->_valuesObjectClass)) {
            throw new Exception('Value Object Class Not set');
        }

        return new $this->_valuesObjectClass($row);
    }

    /**
     * @param array|null $row
     * @return ValuesObject
     * @throws Exception
     */
    protected function getSimpleVO(?array $row = null): ValuesObject
    {
        if (empty($this->_simpleValuesObjectClass)) {
            throw new Exception('Simple Value Object Class Not Set');
        }

        return new $this->_simpleValuesObjectClass($row);
    }

    /**
     * @param ValuesObject|null $fullVO
     * @return ValuesObject|null
     * @throws Exception
     */
    protected function simplifyVO(?ValuesObject $fullVO = null): ?ValuesObject
    {
        if (empty($this->_simpleValuesObjectClass)) {
            throw new Exception('Simple Value Object Class Not Set');
        }

        if (empty($fullVO)) {
            return null;
        }

        return new $this->_simpleValuesObjectClass($fullVO->exportRow());
    }

    /**
     * @param array|null $rows
     * @return array|null
     * @throws Exception
     */
    final protected function getVOArray(?array $rows = null): ?array
    {
        $voArray = [];

        if (empty($rows)) {
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
     * @param array|null $rows
     * @return array|null
     * @throws Exception
     */
    final protected function getSimpleVOArray(?array $rows = null): ?array
    {
        $simpleVoArray = [];

        if (empty($rows)) {
            return null;
        }

        foreach ($rows as $row) {
            $simpleValuesObject = $this->getSimpleVO($row);

            if (!empty($simpleValuesObject)) {
                $simpleVoArray[] = $simpleValuesObject;
            }
        }

        return $simpleVoArray;
    }

    /**
     * @throws Exception
     */
    private function _setStore(): void
    {
        $modelClass = get_called_class();

        $modelName = explode('\\', $modelClass);
        $modelName = end($modelName);

        $storeClass = sprintf('%s\\%sStore', $modelClass, $modelName);

        if (class_exists($storeClass)) {
            $databaseConfig = $this->config->get('database');

            $this->store = new $storeClass($databaseConfig);
        }
    }

    /**
     * @return void
     */
    private function _setValuesObjectClasses(): void
    {
        $modelClass = get_called_class();

        $modelName = explode('\\', $modelClass);
        $modelName = end($modelName);

        $simpleValuesObjectClass = sprintf(
            '%s\\%sSimpleValuesObject',
            $modelClass,
            $modelName
        );

        if (class_exists($simpleValuesObjectClass)) {
            $this->_simpleValuesObjectClass = $simpleValuesObjectClass;
        }

        $valuesObjectClass = sprintf(
            '%s\\%sValuesObject',
            $modelClass,
            $modelName
        );

        if (class_exists($valuesObjectClass)) {
            $this->_valuesObjectClass = $valuesObjectClass;
        }
    }

    /**
     * @return void
     */
    private function _setApi(): void
    {
        $modelClass = get_called_class();

        $modelName = explode('\\', $modelClass);
        $modelName = end($modelName);

        $apiClass = sprintf('%s\\%sApi', $modelClass, $modelName);

        if (class_exists($apiClass)) {
            $this->api = new $apiClass($this);
        }
    }
}
