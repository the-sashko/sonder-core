<?php
namespace Core\Plugins\Session\Classes;

use Core\Plugins\Session\Interfaces\ISessionValuesObject;

use Core\Plugins\Session\Exceptions\SessionException;

class SessionValuesObject implements ISessionValuesObject
{
    /**
     * @var array List Of Model Instance Data
     */
    public $data = [];

    public function __construct(?array $data = null)
    {
        if (!empty($data)) {
            $this->data = $data;
        }
    }

    /**
     * Get Data Of Model Instance
     *
     * @param string|null $valueName Data Name
     *
     * @return mixed Data Value
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
            $errorMessage = SessionException::MESSAGE_VALUE_NAME_IS_NOT_SET;

            $errorMessage = sprintf(
                '%s. Value: "%s"',
                $errorMessage,
                $valueName
            );

            throw new SessionException(
                $errorMessage,
                SessionException::CODE_VALUE_NAME_IS_NOT_SET
            );
        }

        return $this->data[$valueName];
    }

    /**
     * Set Data Of Model Instance
     *
     * @param string|null $valueName Data Name
     * @param mixed       $value     Data Value
     */
    public function set(?string $valueName = null, $value = null): void
    {
        if (empty($valueName)) {
            throw new SessionException(
                SessionException::MESSAGE_VALUE_NAME_IS_NOT_SET,
                SessionException::CODE_VALUE_NAME_IS_NOT_SET
            );
        }

        $this->data[$valueName] = $value;
    }

    /**
     * Check Is Data Value Exists
     *
     * @param string|null $valueName Data Name
     *
     * @return bool Is Data Value Exists
     */
    public function has(?string $valueName = null): bool
    {
        if (empty($valueName)) {
            throw new SessionException(
                SessionException::MESSAGE_VALUE_NAME_IS_NOT_SET,
                SessionException::CODE_VALUE_NAME_IS_NOT_SET
            );
        }

        return array_key_exists($valueName, $this->data);
    }
}
