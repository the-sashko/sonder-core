<?php

namespace Sonder\Core;

use Sonder\Interfaces\IValuesObject;
use Sonder\Exceptions\AppException;
use Sonder\Exceptions\ValuesObjectException;

#[IValuesObject]
class ValuesObject implements IValuesObject
{
    private array $_values;

    /**
     * @param array|null $values
     */
    public function __construct(?array $values = null)
    {
        $this->_values = $values ?? [];
    }

    /**
     * @return array
     */
    final public function __serialize(): array
    {
        return $this->exportRow();
    }

    /**
     * @param array $values
     * @return void
     */
    final public function __unserialize(array $values): void
    {
        $this->_values = $values;
    }

    /**
     * @return array
     */
    final public function jsonSerialize(): array
    {
        return $this->exportRow();
    }

    /**
     * @return array
     */
    public function exportRow(): array
    {
        $makeRow = function (mixed $value) use (&$makeRow): mixed {
            if ($value instanceof ValuesObject) {
                return $value->exportRow();
            }

            if (!is_array($value)) {
                return $value;
            }

            $value = array_filter(
                $value,
                fn(mixed $subValue): bool => (
                    is_scalar($subValue) ||
                    is_array($subValue) ||
                    is_null($subValue) ||
                    ($subValue instanceof ValuesObject)
                )
            );

            return array_map($makeRow, $value);
        };

        return $makeRow($this->_values);
    }

    /**
     * @param string $valueName
     * @return mixed
     * @throws ValuesObjectException
     */
    final protected function get(string $valueName): mixed
    {
        if ($this->has($valueName)) {
            return $this->_values[$valueName];
        }

        $errorMessage = sprintf(
            ValuesObjectException::MESSAGE_VALUES_OBJECT_VALUE_NOT_FOUND,
            $valueName,
            static::class
        );

        throw new ValuesObjectException(
            $errorMessage,
            AppException::CODE_VALUES_OBJECT_VALUE_NOT_FOUND
        );
    }

    /**
     * @param string $valueName
     * @param mixed|null $value
     * @return void
     * @throws ValuesObjectException
     */
    final protected function set(string $valueName, mixed $value = null): void
    {
        if (!empty($valueName)) {
            $this->_values[$valueName] = $value;

            return;
        }

        $errorMessage = sprintf(
            ValuesObjectException::MESSAGE_VALUES_OBJECT_EMPTY_VALUE_NAME,
            static::class
        );

        throw new ValuesObjectException(
            $errorMessage,
            AppException::CODE_VALUES_OBJECT_EMPTY_VALUE_NAME
        );
    }

    /**
     * @param string $valueName
     * @return bool
     */
    final protected function has(string $valueName): bool
    {
        if (empty($valueName)) {
            return false;
        }

        return array_key_exists($valueName, $this->_values);
    }
}
