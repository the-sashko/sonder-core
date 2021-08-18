<?php

/**
 * Class For Working With Form Input Values
 */
abstract class FormObject extends ValuesObject
{
    /**
     * @var string Default Error Message
     */
    const DEFAULT_ERROR_MESSAGE = 'Unknown Error';

    /**
     * @var bool Status Of Processing Form
     */
    public $status = false;

    /**
     * @var array List Of Errors
     */
    public $errors = [];

    public function __construct(?array $values = null)
    {
        parent::__construct($values);

        $this->checkInputValues();
    }

    /**
     * Check Input Values
     */
    abstract public function checkInputValues(): void;

    /**
     * Get Processing Form Status
     *
     * @return bool Processing Form Status Value
     */
    public function getStatus(): bool
    {
        return $this->status;
    }

    /**
     * Get Form Errors
     *
     * @return array|null List Of Errors
     */
    public function getErrors(): ?array
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

    /**
     * Set Processing Form Success Status
     */
    public function setStatusSuccess(): void
    {
        $this->_setStatus(true);
    }

    /**
     * Set Processing Form Fail Status
     */
    public function setStatusFail(): void
    {
        $this->_setStatus(false);
    }

    /**
     * Set Form Errors
     *
     * @param array|null $errors List Of Errors
     */
    public function setErrors(?array $errors = null): void
    {
        $this->errors = array_merge($this->errors, (array)$errors);
        $this->errors = array_unique($this->errors);

        $this->setStatusFail();
    }

    /**
     * Set Form Error
     *
     * @param string|null $error Error Message
     */
    public function setError(?string $error = null): void
    {
        $this->errors[] = $error;
        $this->errors = array_unique($this->errors);

        $this->setStatusFail();
    }

    /**
     * Is Has Form Errors
     *
     * @return bool Is Form Object Has Errors
     */
    private function _hasErrors(): bool
    {
        if ($this->getStatus()) {
            return false;
        }

        return !empty($this->errors);
    }

    /**
     * Set Processing Form Status
     *
     * @param bool $status Processing Form Status Value
     */
    private function _setStatus(bool $status = false): void
    {
        $this->status = $status;
    }
}
