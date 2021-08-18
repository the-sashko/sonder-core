<?php

/**
 * Value Object Class For Setting And Getting Values Of Model Instance
 */
class ValuesObject
{
    /**
     * @var array List Of Model Instance Values
     */
    public $values = [];

    public function __construct(?array $values = null)
    {
        if (!empty($values)) {
            $this->values = $values;
        }
    }

    /**
     * Get Value
     *
     * @param string|null $valueName Value Name
     *
     * @return mixed Value
     */
    public function get(?string $valueName = null)
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

        return $this->values[$valueName];
    }

    /**
     * Get Multiple Values From Model In PHP Array Format
     *
     * @param array|null $params List Of Values
     *
     * @return array|null Array Of Values
     */
    public function exportRow(?array $params = null): ?array
    {
        $row = $this->values;

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
     * Set Value
     *
     * @param string|null $valueName Value Name
     * @param mixed $value Value
     */
    public function set(?string $valueName = null, $value = null): void
    {
        if (empty($valueName)) {
            throw new Exception('Value Name Of ValuesObject Is Empty');
        }

        $this->values[$valueName] = $value;
    }

    /**
     * Check Is Value Exists
     *
     * @param string|null $valueName Value Name
     *
     * @return bool Is Value Exists
     */
    public function has(?string $valueName = null): bool
    {
        if (empty($valueName)) {
            throw new Exception('Value Name Of ValuesObject Is Empty');
        }

        return array_key_exists($valueName, $this->values);
    }
}
