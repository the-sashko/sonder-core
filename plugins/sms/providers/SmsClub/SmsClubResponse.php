<?php
class SmsClubResponse implements ISmsResponse
{
    private $_data = null;

    public function __construct(?array $data = null)
    {
        $this->_data = (array) $data;
    }

    public function getStatus(): bool
    {
        if (!array_key_exists('status', $this->_data)) {
            return false;
        }

        return (bool) $this->_data['status'];
    }

    public function getErrorMessage(): ?string
    {
        if (!array_key_exists('error_message', $this->_data)) {
            return null;
        }

        return (string) $this->_data['error_message'];
    }

    public function getRemoteMessageCode(): ?string
    {
        if (!array_key_exists('remote_message_code', $this->_data)) {
            return null;
        }

        return (string) $this->_data['remote_message_code'];
    }

    public function setStatus(bool $status = false): void
    {
        $this->_data['status'] = $status;
    }

    public function setErrorMessage(?string $errorMessage = null): void
    {
        $this->_data['error_message'] = $errorMessage;
    }

    public function setRemoteMessageCode(
        ?string $remoteMessageCode = null
    ): void
    {
        $this->_data['remote_message_code'] = $remoteMessageCode;
    }
}
