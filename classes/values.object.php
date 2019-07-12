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

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Get Data Of Model Instance
     *
     * @param string $valueName Data Name
     *
     * @return mixed Data Value
     */
    public function get(string $valueName = '')
    {
        if (!$this->has($valueName)) {
            throw new Exception("Value {$valueName} Not Found");
        }

        return $this->data[$valueName];
    }

    /**
     * Get Mustiple Params From Model Data In JSON Format
     *
     * @param array $params List Of Values
     *
     * @return string Data Values In JSON Format
     */
    public function getJSON(array $params = [])
    {
        $res = [];

        foreach ($params as $param) {
            $res[$param] = '';

            if ($this->has($param)) {
                $res[$param] = (string) $this->get($param);
            }
        }

        return json_encode($res);
    }

    /**
     * Get Mustiple Params From Model Data In PHP Array Format
     *
     * @param array $params List Of Values
     *
     * @return array Array Of Data Values
     */
    public function getArray(array $params = [])
    {
        $res = [];

        foreach ($params as $param) {
            $res[$param] = '';

            if ($this->has($param)) {
                $res[$param] = (string) $this->get($param);
            }
        }

        return $res;
    }

    /**
     * Set Data Of Model Instance
     *
     * @param string $valueName Data Name
     * @param mixed  $value     Data Value
     */
    public function set(string $valueName = '', $value = NULL) : void
    {
        $this->data[$valueName] = $value;
    }

    /**
     * Check Is Data Value Exists
     *
     * @param string $valueName Data Name
     *
     * @return bool Is Data Value Exists
     */
    public function has(string $valueName = '') : bool
    {
        return array_key_exists($valueName, $this->data);
    }
}
?>
