<?php
class ValuesObject
{
    public $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function get(string $valueName = '')
    {
        if (!$this->has($valueName)) {
            throw new Exception("Value {$valueName}");
        }

        return $this->data[$valueName];
    }

    public function set(string $valueName = '', $value = NULL) : bool
    {
        $this->data[$valueName] = $value;

        return true;
    }

    public function has(string $valueName = '')
    {
        return array_key_exists($valueName, $this->data);
    }
}
?>