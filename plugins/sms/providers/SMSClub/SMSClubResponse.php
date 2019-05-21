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
        if (!array_key_exists('error', $this->_data)) {
            return '';
        }

        return (string) $this->_data['error'];
    }

    public function getrMessageCode() : string
    {
        if (!array_key_exists('remote_message_id', $this->_data)) {
            return '';
        }

        return (string) $this->_data['remote_message_id'];
    }
}
?>