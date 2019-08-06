<?php
class SMTPResponse implements IMailResponse
{
    const DEFAULT_ERROR_MESSAGE = 'Unknown Server Error';

    private $_status = FALSE;

    private $_errorMessage = NULL;

    public function getStatus() : bool
    {
        return $this->_status;
    }

    private function _setStatus(bool $status = FALSE) : void
    {
        $this->_status = (bool) $status;
    }

    public function setStatusSuccess() : void
    {
        $this->_setStatus(TRUE);
    }

    public function setStatusFail() : void
    {
        if (empty($this->_errorMessage)) {
            $this->_setDefaultErrorMessage();
        }

        $this->_setStatus(FALSE);
    }

    public function setErrorMessage(?string $errorMessage = NULL) : void
    {
        if (empty($errorMessage)) {
            $errorMessage = static::DEFAULT_ERROR_MESSAGE;
        }

        $this->_errorMessage = $errorMessage;

        $this->setStatusFail();
    }

    private function _setDefaultErrorMessage() : void
    {
        $this->_errorMessage = static::DEFAULT_ERROR_MESSAGE;
    }

    public function getErrorMessage() : ?string
    {
        return $this->_errorMessage;
    }
}