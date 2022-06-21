<?php

namespace Sonder\Core;

use Sonder\Enums\ConfigNamesEnum;
use Sonder\Exceptions\AppException;
use Sonder\Exceptions\ConfigException;
use Sonder\Exceptions\ModelException;
use Sonder\Interfaces\IModel;
use Sonder\Interfaces\IModelApi;
use Sonder\Interfaces\IModelFormObject;
use Sonder\Interfaces\IModelSimpleValuesObject;
use Sonder\Interfaces\IModelStore;
use Sonder\Interfaces\IModelValuesObject;

#[IModel]
class CoreModel extends CoreObject implements IModel
{
    protected const ITEMS_ON_PAGE = 10;

    /**
     * @var IModelApi|null
     */
    #[IModelApi]
    public ?IModelApi $api = null;

    /**
     * @var IModelStore|null
     */
    #[IModelStore]
    protected ?IModelStore $store = null;

    /**
     * @var string|null
     */
    private ?string $_valuesObjectClass = null;

    /**
     * @var string|null
     */
    private ?string $_simpleValuesObjectClass = null;

    /**
     * @throws ConfigException
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
     * @return IModelFormObject|null
     */
    final public function getForm(
        ?array $formValues = null,
        ?string $formName = null
    ): ?IModelFormObject {
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
            '%s\\Forms\\%sForm',
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
     * @return IModelValuesObject
     * @throws ModelException
     */
    protected function getVO(?array $row = null): IModelValuesObject
    {
        if (empty($this->_valuesObjectClass)) {
            throw new ModelException(
                ModelException::MESSAGE_MODEL_VALUES_OBJECT_CLASS_NOT_EXISTS,
                AppException::CODE_MODEL_VALUES_OBJECT_CLASS_NOT_EXISTS
            );
        }

        return new $this->_valuesObjectClass($row);
    }

    /**
     * @param array|null $row
     * @return IModelSimpleValuesObject
     * @throws ModelException
     */
    protected function getSimpleVO(?array $row = null): IModelSimpleValuesObject
    {
        if (empty($this->_simpleValuesObjectClass)) {
            throw new ModelException(
                ModelException::MESSAGE_MODEL_SIMPLE_VALUES_OBJECT_CLASS_NOT_EXISTS,
                AppException::CODE_MODEL_SIMPLE_VALUES_OBJECT_CLASS_NOT_EXISTS
            );
        }

        return new $this->_simpleValuesObjectClass($row);
    }

    /**
     * @param IModelValuesObject|null $fullVO
     * @return IModelSimpleValuesObject|null
     * @throws ModelException
     */
    protected function simplifyVO(
        ?IModelValuesObject $fullVO = null
    ): ?IModelSimpleValuesObject {
        if (empty($this->_simpleValuesObjectClass)) {
            throw new ModelException(
                ModelException::MESSAGE_MODEL_SIMPLE_VALUES_OBJECT_CLASS_NOT_EXISTS,
                AppException::CODE_MODEL_SIMPLE_VALUES_OBJECT_CLASS_NOT_EXISTS
            );
        }

        if (empty($fullVO)) {
            return null;
        }

        return new $this->_simpleValuesObjectClass($fullVO->exportRow());
    }

    /**
     * @param array|null $rows
     * @return array|null
     * @throws ModelException
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
     * @throws ModelException
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
     * @return void
     * @throws ConfigException
     */
    private function _setStore(): void
    {
        $modelClass = get_called_class();

        $modelName = explode('\\', $modelClass);
        $modelName = end($modelName);

        $storeClass = sprintf('%s\\%sStore', $modelClass, $modelName);

        if (class_exists($storeClass)) {
            $databaseConfig = $this->config->get(ConfigNamesEnum::DATABASE);

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
            '%s\\ValuesObjects\\%sSimpleValuesObject',
            $modelClass,
            $modelName
        );

        if (class_exists($simpleValuesObjectClass)) {
            $this->_simpleValuesObjectClass = $simpleValuesObjectClass;
        }

        $valuesObjectClass = sprintf(
            '%s\\ValuesObjects\\%sValuesObject',
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
