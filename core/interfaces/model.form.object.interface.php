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

    /**
     * @return void
     */
    public function setStatusSuccess(): void;

    /**
     * @return void
     */
    public function setStatusFail(): void;

    /**
     * @param array|null $errors
     * @return void
     */
    public function setErrors(?array $errors = null): void;

    /**
     * @param string|null $error
     * @return void
     */
    public function setError(?string $error = null): void;
}
