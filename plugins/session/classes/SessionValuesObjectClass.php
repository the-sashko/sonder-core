<?php

namespace Sonder\Plugins\Session\Classes;

use Sonder\Plugins\Session\Exceptions\SessionException;
use Sonder\Plugins\Session\Interfaces\ISessionValuesObject;

final class SessionValuesObject implements ISessionValuesObject
{
    /**
     * @var array
     */
    private array $_data = [];

    /**
     * @var mixed|array
     */
    private mixed $_flashData = [];

    /**
     * @param array|null $data
     *
     * @throws SessionException
     */
    final public function __construct(?array $data = null)
    {
        if (!empty($data)) {
            $this->_data = $data;
        }

        if ($this->has('flash_data')) {
            $this->_flashData = $this->get('flash_data');

            unset($this->_data['flash_data']);
        }
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
        if (empty($valueName)) {
            throw new SessionException(
                SessionException::MESSAGE_VALUE_NAME_IS_NOT_SET,
                SessionException::CODE_VALUE_NAME_IS_NOT_SET
            );
        }

        if (!$this->has($valueName)) {
            $errorMessage = sprintf(
                '%s. Value: "%s"',
                SessionException::MESSAGE_VALUE_NAME_IS_NOT_SET,
                $valueName
            );

            throw new SessionException(
                $errorMessage,
                SessionException::CODE_VALUE_NAME_IS_NOT_SET
            );
        }

        return $this->_data[$valueName];
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
        if (empty($valueName)) {
            throw new SessionException(
                SessionException::MESSAGE_FLASH_VALUE_NAME_IS_NOT_SET,
                SessionException::CODE_FLASH_VALUE_NAME_IS_NOT_SET
            );
        }

        if (!$this->hasFlash($valueName)) {
            $errorMessage = sprintf(
                '%s. Value: "%s"',
                SessionException::MESSAGE_FLASH_VALUE_NAME_IS_NOT_SET,
                $valueName
            );

            throw new SessionException(
                $errorMessage,
                SessionException::CODE_FLASH_VALUE_NAME_IS_NOT_SET
            );
        }

        $valueData = $this->_flashData[$valueName];

        unset($this->_flashData[$valueName]);

        return $valueData;
    }

    /**
     * @param string|null $valueName
     *
     * @param mixed|null $valueData
     *
     * @throws SessionException
     */
    final public function set(
        ?string $valueName = null,
        mixed   $valueData = null
    ): void
    {
        if (empty($valueName)) {
            throw new SessionException(
                SessionException::MESSAGE_VALUE_NAME_IS_NOT_SET,
                SessionException::CODE_VALUE_NAME_IS_NOT_SET
            );
        }

        $this->_data[$valueName] = $valueData;
    }

    /**
     * @param string|null $valueName
     *
     * @param mixed|null $valueData
     *
     * @throws SessionException
     */
    final public function setFlash(
        ?string $valueName = null,
        mixed   $valueData = null
    ): void
    {
        if (empty($valueName)) {
            throw new SessionException(
                SessionException::MESSAGE_FLASH_VALUE_NAME_IS_NOT_SET,
                SessionException::CODE_FLASH_VALUE_NAME_IS_NOT_SET
            );
        }

        $this->_flashData[$valueName] = $valueData;
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

        unset($this->_data[$valueName]);

        return true;
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
        if (empty($valueName)) {
            throw new SessionException(
                SessionException::MESSAGE_VALUE_NAME_IS_NOT_SET,
                SessionException::CODE_VALUE_NAME_IS_NOT_SET
            );
        }

        return array_key_exists($valueName, $this->_data);
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
        if (empty($valueName)) {
            throw new SessionException(
                SessionException::MESSAGE_FLASH_VALUE_NAME_IS_NOT_SET,
                SessionException::CODE_FLASH_VALUE_NAME_IS_NOT_SET
            );
        }

        return array_key_exists($valueName, $this->_flashData);
    }
}
