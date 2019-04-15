<?php
/**
 * Value Object Class For Setting And Getting Data Of Model Instance
 */
class ValuesObject
{
    /**
     * @var List Of Model Instance Data 
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