<?php

namespace Sonder\Core;

use Exception;

class ValuesObject
{
    /**
     * @var array
     */
    private array $_values = [];

    /**
     * @param array|null $values
     */
    public function __construct(?array $values = null)
    {
        if (!empty($values)) {
            $this->_values = $values;
        }
    }

    /**
     * @param array|null $params
     *
     * @return array|null
     */
    public function exportRow(?array $params = null): ?array
    {
        $row = $this->getAll();

        foreach ($row as $param => $value) {
            if (!is_scalar($value) && !is_null($value)) {
                unset($row[$param]);
            }
        }

        if (empty($params)) {
            return $row;
        }

        foreach ($params as $param) {
            if (array_key_exists($param, $row)) {
                unset($row[$param]);
            }
        }

        return $row;
    }

    /**
     * @return array
     */
    final protected function getAll(): array
    {
        return $this->_values;
    }

    /**
     * @param string|null $valueName
     *
     * @return mixed
     *
     * @throws Exception
     */
    final protected function get(?string $valueName = null): mixed
    {
        if (empty($valueName)) {
            throw new Exception('Value Name Of ValuesObject Is Empty');
        }

        if (!$this->has($valueName)) {
            $errorMessage = sprintf(
                'Value %s Not Found In ValuesObject',
                $valueName
            );
            throw new Exception($errorMessage);
        }

        return $this->_values[$valueName];
    }

    /**
     * @param string|null $valueName
     * @param mixed|null $value
     *
     * @throws Exception
     */
    final protected function set(
        ?string $valueName = null,
        mixed   $value = null
    ): void
    {
        if (empty($valueName)) {
            throw new Exception('Value Name Of ValuesObject Is Empty');
        }

        $this->_values[$valueName] = $value;
    }

    /**
     * @param string|null $valueName
     *
     * @return bool
     */
    final protected function has(?string $valueName = null): bool
    {
        if (empty($valueName)) {
            return false;
        }

        return array_key_exists($valueName, $this->_values);
    }
}
