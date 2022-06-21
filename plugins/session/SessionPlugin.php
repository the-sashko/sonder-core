<?php

namespace Sonder\Plugins;

use Attribute;
use Sonder\Plugins\Session\Classes\SessionSecurity;
use Sonder\Plugins\Session\Classes\SessionValuesObject;
use Sonder\Plugins\Session\Exceptions\SessionException;
use Sonder\Plugins\Session\Interfaces\ISessionPlugin;

#[ISessionPlugin]
final class SessionPlugin implements ISessionPlugin
{
    #[SessionValuesObject]
    private SessionValuesObject $_values;

    /**
     * @throws SessionException
     */
    final public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = (new SessionSecurity)->escapeInput($_SESSION);

        $this->_values = new SessionValuesObject($_SESSION);
    }

    /**
     * @param string $valueName
     * @return mixed
     * @throws SessionException
     */
    final public function get(string $valueName): mixed
    {
        if (empty($valueName) || !$this->_values->has($valueName)) {
            return null;
        }

        return $this->_values->get($valueName);
    }

    /**
     * @param string $valueName
     * @return mixed
     * @throws SessionException
     */
    final public function getFlash(string $valueName): mixed
    {
        if (empty($valueName) || !$this->_values->hasFlash($valueName)) {
            return null;
        }

        $flashValuesName = SessionValuesObject::FLASH_VALUES_NAME;

        if (array_key_exists($flashValuesName, $_SESSION)) {
            $_SESSION[$flashValuesName][$valueName] = null;
        }

        return $this->_values->getFlash($valueName);
    }

    /**
     * @param string $valueName
     * @param mixed|null $valueData
     * @return void
     * @throws SessionException
     */
    final public function set(string $valueName, mixed $valueData = null): void
    {
        $this->_values->set($valueName, $valueData);

        $_SESSION[$valueName] = $valueData;
    }

    /**
     * @param string $valueName
     * @param mixed|null $valueData
     * @return void
     * @throws SessionException
     */
    final public function setFlash(
        string $valueName,
        mixed $valueData = null
    ): void {
        $this->_values->setFlash($valueName, $valueData);

        $flashValuesName = SessionValuesObject::FLASH_VALUES_NAME;

        if (!array_key_exists($flashValuesName, $_SESSION)) {
            $_SESSION[$flashValuesName] = [];
        }

        $_SESSION[$flashValuesName][$valueName] = $valueData;
    }

    /**
     * @param string|null $valueName
     * @return bool
     */
    final public function remove(?string $valueName = null): bool
    {
        if (empty($valueName) || !$this->has($valueName)) {
            return false;
        }

        unset($_SESSION[$valueName]);

        return $this->_values->remove($valueName);
    }

    /**
     * @param string $valueName
     * @return bool
     */
    final public function has(string $valueName): bool
    {
        return $this->_values->has($valueName);
    }

    /**
     * @param string $valueName
     * @return bool
     */
    final public function hasFlash(string $valueName): bool
    {
        return $this->_values->hasFlash($valueName);
    }
}
