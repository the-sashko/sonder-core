<?php
/**
 * Value Object Class For Setting And Getting Data Of Model Instance
 */
class ValuesObject
{
    /**
    * @var string Default Error Message
    */
    const DEFAULT_ERROR_MESSAGE = 'Unknown Error';

    /**
     * @var array List Of Model Instance Data
     */
    public $data = [];

    public function __construct(?array $data = null)
    {
        if (!empty($data)) {
            $this->data = $data;
        }
    }

    /**
     * Get Data Of Model Instance
     *
     * @param string|null $valueName Data Name
     *
     * @return mixed Data Value
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

        return $this->data[$valueName];
    }

    /**
     * Get Mustiple Params From Model Data In JSON Format
     *
     * @param array|null $params List Of Values
     *
     * @return string|null Data Values In JSON Format
     */
    public function getJSON(?array $params = null): ?string
    {
        $res = [];

        if (empty($params)) {
            return null;
        }

        foreach ($params as $param) {
            $res[$param] = null;

            if ($this->has($param)) {
                $res[$param] = (string) $this->get($param);
            }
        }

        if (empty($res)) {
            return null;
        }

        return json_encode($res);
    }

    /**
     * Get Mustiple Params From Model Data In PHP Array Format
     *
     * @param array|null $params List Of Values
     *
     * @return array|null Array Of Data Values
     */
    public function getArray(?array $params = null): ?array
    {
        $res = [];

        if (empty($params)) {
            return null;
        }

        foreach ($params as $param) {
            $res[$param] = null;

            if ($this->has($param)) {
                $res[$param] = (string) $this->get($param);
            }
        }

        return $res;
    }

    /**
     * Set Data Of Model Instance
     *
     * @param string|null $valueName Data Name
     * @param mixed       $value     Data Value
     */
    public function set(?string $valueName = null, $value = null): void
    {
        if (empty($valueName)) {
            throw new Exception('Value Name Of ValuesObject Is Empty');
        }

        $this->data[$valueName] = $value;
    }

    /**
     * Check Is Data Value Exists
     *
     * @param string|null $valueName Data Name
     *
     * @return bool Is Data Value Exists
     */
    public function has(?string $valueName = null): bool
    {
        if (empty($valueName)) {
            throw new Exception('Value Name Of ValuesObject Is Empty');
        }

        return array_key_exists($valueName, $this->data);
    }
}
