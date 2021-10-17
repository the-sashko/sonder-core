<?php

namespace Sonder\Plugins\Sms\Platforms;

use Sonder\Plugins\Sms\Interfaces\ISmsResponse;

final class SmsClubResponse implements ISmsResponse
{
    /**
     * @var array
     */
    private array $_values;

    /**
     * @param array|null $data
     */
    final public function __construct(?array $data = null)
    {
        $this->_values = (array) $data;
    }

    /**
     * @return bool
     */
    final public function getStatus(): bool
    {
        if (!array_key_exists('status', $this->_values)) {
            return false;
        }

        return (bool) $this->_values['status'];
    }

    /**
     * @return string|null
     */
    final public function getErrorMessage(): ?string
    {
        if (!array_key_exists('error_message', $this->_values)) {
            return null;
        }

        return (string) $this->_values['error_message'];
    }

    /**
     * @return string|null
     */
    final public function getRemoteMessageCode(): ?string
    {
        if (!array_key_exists('remote_message_code', $this->_values)) {
            return null;
        }

        return (string) $this->_values['remote_message_code'];
    }

    /**
     * @param bool $status
     */
    final public function setStatus(bool $status = false): void
    {
        $this->_values['status'] = $status;
    }

    /**
     * @param string|null $errorMessage
     */
    final public function setErrorMessage(?string $errorMessage = null): void
    {
        $this->_values['error_message'] = $errorMessage;
    }

    /**
     * @param string|null $remoteMessageCode
     */
    final public function setRemoteMessageCode(
        ?string $remoteMessageCode = null
    ): void
    {
        $this->_values['remote_message_code'] = $remoteMessageCode;
    }
}
