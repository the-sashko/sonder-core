<?php
class ModelApiResultObject
{
    const DEFAULT_ERROR_MESSAGE = 'Unknown API Error';

    const EMPTY_ERROR_MESSAGE = 'Empty';

    const METHOD_IS_NOT_IMPLEMENTED = 'This API Method Is Not Implemented Yet';

    /**
     * @var ?array
     */
    private ?array $_values = null;

    /**
     * @var bool
     */
    private bool $_status = false;

    /**
     * @var array
     */
    private array $_errors = [];

    final public function getStatus(): bool
    {
        if (!empty($this->_errors)) {
            $this->_status = false;
        }

        return $this->_status;
    }

    /**
     * @return array|null
     */
    final public function getValues(): ?array
    {
        if (empty($this->_values)) {
            $this->setError(static::EMPTY_ERROR_MESSAGE);
            $this->_values = null;
        }

        return $this->_values;
    }

    /**
     * @return array|null
     */
    final public function getErrors(): ?array
    {
        $errors = $this->_errors;

        if ($this->getStatus() && !empty($errors)) {
            $this->setStatusFail();
        }

        if (!$this->getStatus() && empty($errors)) {
            $errors[] = static::DEFAULT_ERROR_MESSAGE;
        }

        $this->_errors = $errors;

        return $errors;
    }

    final public function setStatusSuccess(): void
    {
        $this->_setStatus(true);
    }

    final public function setStatusFail(): void
    {
        $this->_setStatus(false);
        $this->_values = null;
    }

    /**
     * @param array|null $errors
     */
    final public function setErrors(?array $errors = null): void
    {
        $this->_errors = array_merge($this->_errors, (array) $errors);
        $this->_errors = array_unique($this->_errors);

        $this->setStatusFail();
    }

    /**
     * @param string|null $error
     */
    final public function setError(?string $error = null): void
    {
        $this->_errors[] = $error;
        $this->_errors = array_unique($this->_errors);

        $this->setStatusFail();
    }

    /**
     * @param array|null $values
     */
    final public function setValues(?array $values = null): void
    {
        if (empty($values)) {
            $this->setError(static::EMPTY_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        $this->_values = $values;
    }

    /**
     * @param bool $status
     */
    private function _setStatus(bool $status = false): void
    {
        $this->_status = $status;
    }
}
