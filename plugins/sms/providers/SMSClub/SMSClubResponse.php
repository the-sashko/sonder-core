<?php
class SMSClubResponse implements ISMSResponse
{
    private $_data = [];

    public function __construct($data = [])
    {
        $this->_data = $data;
    }

    public function getStatus() : bool
    {
        if (!array_key_exists('status', $this->_data)) {
            return FALSE;
        }

        return (bool) $this->_data['status'];
    }

    public function getErrorMessage() : string
    {
        if (!array_key_exists('error_message', $this->_data)) {
            return '';
        }

        return (string) $this->_data['error_message'];
    }

    public function getRemoteMessageCode() : string
    {
        if (!array_key_exists('remote_message_code', $this->_data)) {
            return '';
        }

        return (string) $this->_data['remote_message_code'];
    }

    public function setStatus(bool $status = FALSE) : void
    {
        $this->_data['status'] = $status;
    }

    public function setErrorMessage(string $errorMessage = '') : void
    {
        $this->_data['error_message'] = $errorMessage;
    }

    public function setRemoteMessageCode(
        string $remoteMessageCode = ''
    ) : void
    {
        $this->_data['remote_message_code'] = $remoteMessageCode;
    }
}
?>