<?php
/**
 * Class For Working With Form Data
 */
abstract class FormObject extends ValuesObject
{
    /**
    * @var string Default Error Message
    */
    const DEFAULT_ERROR_MESSAGE = 'Unknown Error';

    /**
     * @var array List Of Form Data Values
     */
    public $data = [];

    /**
     * @var bool Status Of Processing Form
     */
    public $status = false;

    /**
     * @var array List Of Errors
     */
    public $errors = [];

    /**
     * @var string Message For User
     */
    public $message = null;

    public function __construct(?array $data = null)
    {
        parent::__construct($data);

        $this->checkInputData();
    }

    /**
     * Check Input Data
     */
    abstract public function checkInputData(): void;

    /**
     * Get Processing Form Status
     *
     * @return bool Processing Form Status Value
     */
    public function isStatusSuccess(): bool
    {
        return $this->status;
    }

    /**
     * Is Has Form Errors
     *
     * @return bool Is Form Object Has Errors
     */
    public function hasErrors(): bool
    {
        if ($this->isStatusSuccess()) {
            return false;
        }

        return !empty($this->errors);
    }

    /**
     * Get Form Errors
     *
     * @return array|null List Of Errors
     */
    public function getErrors(): ?array
    {
        if ($this->isStatusSuccess()) {
            return null;
        }

        if ($this->hasErrors()) {
            return $this->errors;
        }

        return null;
    }

    /**
     * Get Message For User
     *
     * @return string|null Message Value
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * Set Processing Form Status
     *
     * @param bool $status Processing Form Status Value
     */
    public function setStatus(bool $status = false): void
    {
        $this->status = $status;
    }

    /**
     * Set Processing Form Success Status
     */
    public function setSuccess(): void
    {
        $this->setStatus(true);
    }

    /**
     * Set Processing Form Fail Status
     */
    public function setFail(): void
    {
        $this->setStatus(false);
    }

    /**
     * Set Form Errors
     *
     * @param array|null $errors List Of Errors
     */
    public function setErrors(?array $errors = null): void
    {
        $this->errors = array_merge($this->errors, (array) $errors);
        $this->errors = array_unique($this->errors);

        $this->setFail();
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

        $this->setFail();
    }

    /**
     * Set Message For User
     *
     * @param string|null $message Message Value
     */
    public function setMessage(?string $message = null): void
    {
        $this->message = $message;
    }
}
