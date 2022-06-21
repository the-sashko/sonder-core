<?php

namespace Sonder\Plugins\Session\Classes;

use Attribute;
use Sonder\Plugins\Session\Exceptions\SessionException;
use Sonder\Plugins\Session\Interfaces\ISessionValuesObject;

#[ISessionValuesObject]
#[Attribute(Attribute::TARGET_PROPERTY)]
final class SessionValuesObject implements ISessionValuesObject
{
    final public const FLASH_VALUES_NAME = 'flash_values';

    /**
     * @var array
     */
    private array $_values;

    /**
     * @var array|mixed
     */
    private array $_flashValues = [];

    /**
     * @param array|null $values
     * @throws SessionException
     */
    final public function __construct(?array $values = null)
    {
        $this->_values = $values ?? [];

        if ($this->has(SessionValuesObject::FLASH_VALUES_NAME)) {
            $this->_flashValues = (array)$this->get(
                SessionValuesObject::FLASH_VALUES_NAME
            );

            unset($this->_values[SessionValuesObject::FLASH_VALUES_NAME]);
        }
    }

    /**
     * @param string $valueName
     * @return mixed
     * @throws SessionException
     */
    final public function get(string $valueName): mixed
    {
        if (!$this->has($valueName)) {
            $errorMessage = sprintf(
                SessionException::MESSAGE_VALUE_NAME_IS_NOT_SET,
                $valueName
            );

            throw new SessionException(
                $errorMessage,
                SessionException::CODE_VALUE_NAME_IS_NOT_SET
            );
        }

        return $this->_values[$valueName];
    }

    /**
     * @param string $valueName
     * @return mixed
     * @throws SessionException
     */
    final public function getFlash(string $valueName): mixed
    {
        if (!$this->hasFlash($valueName)) {
            $errorMessage = sprintf(
                SessionException::MESSAGE_FLASH_VALUE_NAME_IS_NOT_SET,
                $valueName
            );

            throw new SessionException(
                $errorMessage,
                SessionException::CODE_FLASH_VALUE_NAME_IS_NOT_SET
            );
        }

        $valueData = $this->_flashValues[$valueName];

        unset($this->_flashValues[$valueName]);

        return $valueData;
    }

    /**
     * @param string $valueName
     * @param mixed|null $valueData
     * @return void
     * @throws SessionException
     */
    final public function set(string $valueName, mixed $valueData = null): void
    {
        if (empty($valueName)) {
            throw new SessionException(
                SessionException::MESSAGE_VALUE_NAME_IS_EMPTY,
                SessionException::CODE_VALUE_NAME_IS_EMPTY
            );
        }

        $this->_values[$valueName] = $valueData;
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
        if (empty($valueName)) {
            throw new SessionException(
                SessionException::MESSAGE_FLASH_VALUE_NAME_IS_EMPTY,
                SessionException::CODE_FLASH_VALUE_NAME_IS_EMPTY
            );
        }

        $this->_flashValues[$valueName] = $valueData;
    }

    /**
     * @param string $valueName
     * @return bool
     */
    final public function remove(string $valueName): bool
    {
        if (!$this->has($valueName)) {
            return false;
        }

        unset($this->_values[$valueName]);

        return true;
    }

    /**
     * @param string $valueName
     * @return bool
     */
    final public function has(string $valueName): bool
    {
        return array_key_exists($valueName, $this->_values);
    }

    /**
     * @param string $valueName
     * @return bool
     */
    final public function hasFlash(string $valueName): bool
    {
        return array_key_exists($valueName, $this->_flashValues);
    }
}
