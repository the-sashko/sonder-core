<?php
namespace Core\Plugins\Session\Classes;

use Core\Plugins\Session\Interfaces\ISessionValuesObject;

use Core\Plugins\Session\Exceptions\SessionException;

class SessionValuesObject implements ISessionValuesObject
{
    /**
     * @var array List Of Model Instance Data
     */
    private $_data = [];

    /**
     * @var array List Of Model Instance Single Use Data
     */
    private $_flashData = [];

    public function __construct(?array $data = null)
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
     * Get Data Of Model Instance
     *
     * @param string|null $valueName Value Name
     *
     * @return mixed Value Data
     */
    public function get(?string $valueName = null)
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
     * Get Data Of Model Instance
     *
     * @param string|null $valueName Value Name
     *
     * @return mixed Value Data
     */
    public function getFlash(?string $valueName = null)
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
     * Set Data Of Model Instance
     *
     * @param string|null $valueName Value Name
     * @param mixed       $valueData Value Data
     */
    public function set(?string $valueName = null, $valueData = null): void
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
     * Set Data Of Model Instance
     *
     * @param string|null $valueName Value Name
     * @param mixed       $valueData Value Data
     */
    public function setFlash(
        ?string $valueName = null,
                $valueData = null
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
     * Remove Data From Model Instance
     *
     * @param string|null $valueName Value Name
     */
    public function remove(?string $valueName = null): bool
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
     * Check Is Value Exists
     *
     * @param string|null $valueName Value Name
     *
     * @return bool Is Value Exists
     */
    public function has(?string $valueName = null): bool
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
     * Check Is Value Exists
     *
     * @param string|null $valueName Value Name
     *
     * @return bool Is Value Exists
     */
    public function hasFlash(?string $valueName = null): bool
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
