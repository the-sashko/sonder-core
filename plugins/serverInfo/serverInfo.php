<?php
/**
 * Plugin For Working With Server Meta Data
 */
class ServerInfoPlugin
{
    /**
     * @var array Server Meta Data
     */
    private $_data = [];

    public function __construct()
    {
        $securityPlugin = new SecurityPlugin();
        $_SERVER        = $securityPlugin->escapeInput($_SERVER);
        $this->_data    = new ValuesObject($_SERVER);
    }

    /**
     * Get Server Meta Data By Value Name
     *
     * @param string|null $valueName Name Of Value
     *
     * @return mixed Server Value Data
     */
    public function get(?string $valueName = null)
    {
        if (!$this->_data->has($valueName)) {
            return null;
        }

        return $this->_data->get($valueName);
    }

    /**
     * Check Is Server Meta Data Value Exists
     *
     * @param string|null $vaueName Name Of Value
     *
     * @return bool Is Value Exists In Server Meta Data
     */
    public function has(?string $valueName = null)
    {
        return $this->_data->has($valueName);
    }
}
