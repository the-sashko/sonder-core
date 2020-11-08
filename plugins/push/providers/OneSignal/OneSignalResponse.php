<?php
class OneSignalResponse implements IPushResponse
{
    private $_status = false;

    private $_errorMessage = null;

    private $_remoteCode = null;

    private $_recipientsCount = 0;

    public function setStatus(bool $status = false): void
    {
        $this->_status = $status;
    }

    public function setErrorMessage(?string $errorMessage = null): void
    {
        $this->_errorMessage = $errorMessage;
    }

    public function setRemoteCode(?string $remoteCode = null): void
    {
        $this->_remoteCode = $remoteCode;
    }

    public function setRecipientsCount(int $recipientsCount = 0): void
    {
        $this->_recipientsCount = $recipientsCount;
    }

    public function getStatus(): bool
    {
        return $this->_status;
    }

    public function getErrorMessage(): ?string
    {
        return $this->_errorMessage;
    }

    public function getRemoteCode(): ?string
    {
        return $this->_remoteCode;
    }

    public function getRecipientsCount(): int
    {
        return $this->_recipientsCount;
    }
}
