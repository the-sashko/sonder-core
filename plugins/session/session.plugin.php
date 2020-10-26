<?php
use Core\Plugins\Session\Interfaces\ISessionPlugin;

use Core\Plugins\Session\Classes\SessionSecurity;
use Core\Plugins\Session\Classes\SessionValuesObject;

/**
 * Plugin For Working With Session Data
 */
class SessionPlugin implements ISessionPlugin
{
    /**
     * @var SessionValuesObject|null Session Data
     */
    private $_data = null;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $sessionSecurity = new SessionSecurity();
        $_SESSION        = $sessionSecurity->escapeInput($_SESSION);

        $this->_data = new SessionValuesObject($_SESSION);
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
     * Get Session Data By Value Name (Single Use)
     *
     * @param string|null $valueName Name Of Value
     *
     * @return mixed Session Value Data
     */
    public function getFlash(?string $valueName = null)
    {
        if (!$this->_data->hasFlash($valueName)) {
            return null;
        }

        if (array_key_exists('flash_data', $_SESSION)) {
            $_SESSION['flash_data'][$valueName] = null;
        }

        return $this->_data->getFlash($valueName);
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
     * Set Data To Session Value (Single Use)
     *
     * @param string|null $valueName Name Of Value
     * @param mixed       $valueData Data Of Value
     */
    public function setFlash(
        ?string $valueName = null,
                $valueData = null
    ): void
    {
        $this->_data->setFlash($valueName, $valueData);

        if (!array_key_exists('flash_data', $_SESSION)) {
            $_SESSION['flash_data'] = [];
        }

        $_SESSION['flash_data'][$valueName] = $valueData;
    }

    /**
     * Check Is Session Value Exists
     *
     * @param string|null $valueName Name Of Value
     *
     * @return bool Is Value Exists In Session
     */
    public function has(?string $valueName = null): bool
    {
        return $this->_data->has($valueName);
    }

    /**
     * Check Is Session Single Use Value Exists
     *
     * @param string|null $valueName Name Of Value
     *
     * @return bool Is Value Exists In Session
     */
    public function hasFlash(?string $valueName = null): bool
    {
        return $this->_data->hasFlash($valueName);
    }
}
