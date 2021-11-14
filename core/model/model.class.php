<?php

namespace Sonder\Core;

use Sonder\Core\Interfaces\IModel;
use Exception;

class CoreModel extends CoreObject implements IModel
{
    protected ?object $store = null;

    protected ?object $api = null;

    private ?string $_valuesObjectClass = null;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();

        $this->_setStore();
        $this->_setValuesObject();
        $this->_setApi();
    }

    /**
     * @param array|null $row
     * @return ValuesObject
     * @throws Exception
     */
    final protected function getVO(?array $row = null): ValuesObject
    {
        if (empty($this->_valuesObjectClass)) {
            throw new Exception('Value Object class not set');
        }

        return new $this->_valuesObjectClass($row);
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
     * @throws Exception
     */
    private function _setStore(): void
    {
        $storeClass = sprintf('%sStore', get_called_class());

        if (class_exists($storeClass)) {
            $databaseConfig = $this->config->get('database');

            $this->store = new $storeClass($databaseConfig);
        }
    }

    private function _setValuesObject(): void
    {
        $valuesObjectClass = sprintf('%sValuesObject', get_called_class());

        if (class_exists($valuesObjectClass)) {
            $this->_valuesObjectClass = $valuesObjectClass;
        }
    }

    private function _setApi(): void
    {
        $apiClass = sprintf('%sApi', get_called_class());

        if (class_exists($apiClass)) {
            $this->api = new $apiClass($this);
        }
    }
}
