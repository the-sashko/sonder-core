<?php

namespace Sonder\Core;

use Sonder\Core\Interfaces\IModelFormObject;

abstract class ModelFormObject extends ValuesObject implements IModelFormObject
{
    const DEFAULT_ERROR_MESSAGE = 'Unknown Error';

    /**
     * @var bool
     */
    public bool $status = false;

    /**
     * @var array
     */
    public array $errors = [];

    abstract protected function checkInputValues(): void;

    /**
     * @param array|null $values
     */
    public function __construct(?array $values = null)
    {
        parent::__construct($values);

        $this->checkInputValues();
    }

    /**
     * @return bool
     */
    final public function getStatus(): bool
    {
        return $this->status;
    }

    /**
     * @return array|null
     */
    final public function getErrors(): ?array
    {
        if ($this->_hasErrors()) {
            return $this->errors;
        }

        if ($this->getStatus()) {
            return null;
        }

        return [
            static::DEFAULT_ERROR_MESSAGE
        ];
    }

    final public function setStatusSuccess(): void
    {
        $this->_setStatus(true);
    }

    final public function setStatusFail(): void
    {
        $this->_setStatus();
    }

    /**
     * @param array|null $errors
     */
    final public function setErrors(?array $errors = null): void
    {
        $this->errors = array_merge($this->errors, (array)$errors);
        $this->errors = array_unique($this->errors);

        $this->setStatusFail();
    }

    /**
     * @param string|null $error
     */
    final public function setError(?string $error = null): void
    {
        $this->errors[] = $error;
        $this->errors = array_unique($this->errors);

        $this->setStatusFail();
    }

    /**
     * @return bool
     */
    private function _hasErrors(): bool
    {
        if ($this->getStatus()) {
            return false;
        }

        return !empty($this->errors);
    }

    /**
     * @param bool $status
     */
    private function _setStatus(bool $status = false): void
    {
        $this->status = $status;
    }
}
