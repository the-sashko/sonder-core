<?php

namespace Sonder\Plugins\Mail\Interfaces;

interface IMailResponse
{
    /**
     * @return bool
     */
    public function getStatus(): bool;

    /**
     * @return string|null
     */
    public function getErrorMessage(): ?string;

    public function setStatusFail(): void;

    public function setErrorMessage(?string $errorMessage): void;

    public function setStatusSuccess(): void;
}
