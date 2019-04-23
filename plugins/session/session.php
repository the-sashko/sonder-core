<?php
/**
 * Plugin For Working With Session Data
 */
class SessionPlugin
{
    /**
     * @var array Session Data
     */
    private $_data = [];

    public function __construct()
    {
        $securityPlugin = new SecurityPlugin();
        $_SESSION       = $securityPlugin->escapeInput($_SESSION);
        $this->_data    = new ValueObject($_SESSION);
    }

    /**
     * Get Session Data By Value Name
     *
     * @param string $valueName Name Of Value
     *
     * @return mixed Session Value Data
     */
    public function get(string $valueName = '')
    {
        if (!$this->_data->has($valueName)) {
            return NULL;
        }

        return $this->_data->get($valueName);
    }

    /**
     * Set Data To Session Value
     *
     * @param string $valueName Name Of Value
     * @param mixed  $valueData Data Of Value
     */
    public function set(string $valueName = '', $valueData = NULL)
    {
        $this->_data->set($valueName, $valueData);
        $_SESSION[$valueName] = $valueData;
    }

    /**
     * Check Is Session Value Exists
     *
     * @param string $vaueName Name Of Value
     *
     * @return bool Is Value Exists In Session
     */
    public function has(string $valueName = '')
    {
        return $this->_data->has($valueName);
    }
}
?>
