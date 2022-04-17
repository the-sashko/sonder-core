<?php

namespace Sonder\Plugins;

use Sonder\Plugins\Session\Classes\SessionSecurity;
use Sonder\Plugins\Session\Classes\SessionValuesObject;
use Sonder\Plugins\Session\Exceptions\SessionException;
use Sonder\Plugins\Session\Interfaces\ISessionPlugin;

final class SessionPlugin implements ISessionPlugin
{
    /**
     * @var SessionValuesObject
     */
    private SessionValuesObject $_data;

    /**
     * @throws SessionException
     */
    final public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $sessionSecurity = new SessionSecurity();
        $_SESSION = $sessionSecurity->escapeInput($_SESSION);

        $this->_data = new SessionValuesObject($_SESSION);
    }

    /**
     * @param string|null $valueName
     *
     * @return mixed
     *
     * @throws SessionException
     */
    final public function get(?string $valueName = null): mixed
    {
        if (!$this->_data->has($valueName)) {
            return null;
        }

        return $this->_data->get($valueName);
    }

    /**
     * @param string|null $valueName
     *
     * @return mixed
     *
     * @throws SessionException
     */
    final public function getFlash(?string $valueName = null): mixed
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
     * @param string|null $valueName
     * @param mixed $valueData
     *
     * @throws SessionException
     */
    final public function set(
        ?string $valueName = null,
        mixed   $valueData = null
    ): void
    {
        $this->_data->set($valueName, $valueData);
        $_SESSION[$valueName] = $valueData;
    }

    /**
     * @param string|null $valueName
     * @param mixed $valueData
     *
     * @throws SessionException
     */
    final public function setFlash(
        ?string $valueName = null,
        mixed   $valueData = null
    ): void
    {
        $this->_data->setFlash($valueName, $valueData);

        if (!array_key_exists('flash_data', $_SESSION)) {
            $_SESSION['flash_data'] = [];
        }

        $_SESSION['flash_data'][$valueName] = $valueData;
    }

    /**
     * @param string|null $valueName
     *
     * @return bool
     *
     * @throws SessionException
     */
    final public function remove(?string $valueName = null): bool
    {
        if (empty($valueName)) {
            return false;
        }

        if (!$this->has($valueName)) {
            return false;
        }

        unset($_SESSION[$valueName]);

        return $this->_data->remove($valueName);
    }

    /**
     * @param string|null $valueName
     *
     * @return bool
     *
     * @throws SessionException
     */
    final public function has(?string $valueName = null): bool
    {
        return $this->_data->has($valueName);
    }

    /**
     * @param string|null $valueName
     *
     * @return bool
     *
     * @throws SessionException
     */
    final public function hasFlash(?string $valueName = null): bool
    {
        return $this->_data->hasFlash($valueName);
    }
}
