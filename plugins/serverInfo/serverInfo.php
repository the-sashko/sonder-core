<?php
/**
 * Plugin For Working With Server Meta Data
 */
class SessionPlugin
{
    /**
     * @var array Server Meta Data
     */
    private $_data = [];

    public function __construct()
    {
        $securityPlugin = new SecurityPlugin();
        $_SERVER = $securityPlugin->escapeInput($_SERVER);
        $this->_data = new ValueObject($_SERVER);
    }

    /**
     * Get Server Meta Data By Value Name
     *
     * @param string $valueName Name Of Value
     *
     * @return mixed Server Value Data
     */
    public function get(string $valueName = '')
    {
        if (!$this->_data->has($valueName)) {
            return NULL;
        }

        return $this->_data->get($valueName);
    }

    /**
     * Check Is Server Meta Data Value Exists
     * 
     * @param string $vaueName Name Of Value
     *
     * @return bool Is Value Exists In Server Meta Data
     */
    public function has(string $valueName = '')
    {
        return $this->_data->has($valueName);
    }
}
?>
