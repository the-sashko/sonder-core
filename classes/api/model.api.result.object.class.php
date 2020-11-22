<?php
class ModelApiResultObject
{
    const DEFAULT_ERROR_MESSAGE = 'Unknown API Error';

    const EMPTY_ERROR_MESSAGE = 'Empty';

    private $_values = [];

    private $_status = false;

    private $_errors = [];

    public function getStatus(): bool
    {
        if (!empty($this->_errors)) {
            $this->_status = false;
        }

        return $this->_status;
    }

    public function getValues(): ?array
    {
        if (empty($this->_values)) {
            $this->setError(static::EMPTY_ERROR_MESSAGE);
            $this->_values = null;
        }

        return $this->_values;
    }

    public function getErrors(): ?array
    {
        $errors = $this->_errors;

        if ($this->getStatus() && !empty($errors)) {
            $this->setFail();
        }

        if (!$this->getStatus() && empty($errors)) {
            $errors[] = static::DEFAULT_ERROR_MESSAGE;
        }

        $this->_errors = $errors;

        return $errors;
    }

    public function setStatus(bool $status = false): void
    {
        $this->_status = $status;
    }

    public function setSuccess(): void
    {
        $this->setStatus(true);
    }

    public function setFail(): void
    {
        $this->setStatus(false);
        $this->_values = null;
    }

    public function setErrors(?array $errors = null): void
    {
        $this->_errors = array_merge($this->_errors, (array) $errors);
        $this->_errors = array_unique($this->_errors);

        $this->setFail();
    }

    public function setError(?string $error = null): void
    {
        $this->_errors[] = $error;
        $this->_errors = array_unique($this->_errors);

        $this->setFail();
    }

    public function setValues(?array $values = null): void
    {
        if (empty($values)) {
            $this->setError(static::EMPTY_ERROR_MESSAGE);
            $this->setFail();
        }

        $this->_values = $values;
    }
}
