<?php
class OneSignalResponse implements IPushResponse
{
    private $_status = FALSE;

    private $_errorMessage = '';

    private $_remoteCode = '';

    private $_recipientsCount = 0;

    public function setStatus(bool $status = FALSE) : void
    {
        $this->_status = $status;
    }

    public function setErrorMessage(string $errorMessage = '') : void
    {
        $this->_errorMessage = $errorMessage;
    }

    public function setRemoteCode(string $_remoteCode = '') : void
    {
        $this->_remoteCode = $_remoteCode;
    }

    public function setRecipientsCount(int $recipientsCount = 0) : void
    {
        $this->_recipientsCount = $recipientsCount;
    }

    public function getStatus() : bool
    {
        return $this->_status;
    }

    public function getErrorMessage() : string
    {
        return $this->_errorMessage;
    }

    public function getRemoteCode() : string
    {
        return $this->_remoteCode;
    }

    public function getRecipientsCount() : int
    {
        return $this->_recipientsCount;
    }
}
?>
