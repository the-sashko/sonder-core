<?php
class SmtpResponse implements IMailResponse
{
    const DEFAULT_ERROR_MESSAGE = 'Unknown Server Error';

    private $_status = false;

    private $_errorMessage = null;

    public function getStatus(): bool
    {
        return $this->_status;
    }

    private function _setStatus(bool $status = false): void
    {
        $this->_status = (bool) $status;
    }

    public function setStatusSuccess(): void
    {
        $this->_setStatus(true);
    }

    public function setStatusFail(): void
    {
        if (empty($this->_errorMessage)) {
            $this->_setDefaultErrorMessage();
        }

        $this->_setStatus(false);
    }

    public function setErrorMessage(?string $errorMessage = null): void
    {
        if (empty($errorMessage)) {
            $errorMessage = static::DEFAULT_ERROR_MESSAGE;
        }

        $this->_errorMessage = $errorMessage;

        $this->setStatusFail();
    }

    private function _setDefaultErrorMessage(): void
    {
        $this->_errorMessage = static::DEFAULT_ERROR_MESSAGE;
    }

    public function getErrorMessage(): ?string
    {
        return $this->_errorMessage;
    }
}
