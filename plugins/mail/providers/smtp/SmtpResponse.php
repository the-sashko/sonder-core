<?php

namespace Sonder\Plugins\Mail\Providers\Smtp;

use Sonder\Plugins\Mail\Interfaces\IMailResponse;

final class SmtpResponse implements IMailResponse
{
    private const DEFAULT_ERROR_MESSAGE = 'Unknown Server Error';

    /**
     * @var bool
     */
    private bool $_status = false;

    /**
     * @var string|null
     */
    private ?string $_errorMessage = null;

    /**
     * @return bool
     */
    public function getStatus(): bool
    {
        return $this->_status;
    }

    /**
     * @param bool $status
     */
    private function _setStatus(bool $status): void
    {
        $this->_status = $status;
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

    /**
     * @param string|null $errorMessage
     */
    public function setErrorMessage(?string $errorMessage = null): void
    {
        if (empty($errorMessage)) {
            $errorMessage = SmtpResponse::DEFAULT_ERROR_MESSAGE;
        }

        $this->_errorMessage = $errorMessage;

        $this->setStatusFail();
    }

    private function _setDefaultErrorMessage(): void
    {
        $this->_errorMessage = SmtpResponse::DEFAULT_ERROR_MESSAGE;
    }

    /**
     * @return string|null
     */
    public function getErrorMessage(): ?string
    {
        return $this->_errorMessage;
    }
}
