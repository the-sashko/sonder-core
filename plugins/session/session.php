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

        $this->_data = new ValuesObject($_SESSION);
    }

    /**
     * Get Session Data By Value Name
     *
     * @param string|null $valueName Name Of Value
     *
     * @return mixed Session Value Data
     */
    public function get(?string $valueName = null)
    {
        if (!$this->_data->has($valueName)) {
            return null;
        }

        return $this->_data->get($valueName);
    }

    /**
     * Set Data To Session Value
     *
     * @param string|null $valueName Name Of Value
     * @param mixed       $valueData Data Of Value
     */
    public function set(?string $valueName = null, $valueData = null): void
    {
        $this->_data->set($valueName, $valueData);
        $_SESSION[$valueName] = $valueData;
    }

    /**
     * Check Is Session Value Exists
     *
     * @param string|null $vaueName Name Of Value
     *
     * @return bool Is Value Exists In Session
     */
    public function has(?string $valueName = null): bool
    {
        return $this->_data->has($valueName);
    }
}
