<?php

namespace Sonder\Core\Interfaces;

interface IModelFormObject
{
    /**
     * @return bool
     */
    public function getStatus(): bool;

    /**
     * @return array|null
     */
    public function getErrors(): ?array;

    public function setStatusSuccess(): void;

    public function setStatusFail(): void;

    /**
     * @param array|null $errors
     */
    public function setErrors(?array $errors = null): void;

    /**
     * @param string|null $error
     */
    public function setError(?string $error = null): void;
}
