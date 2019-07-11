<?php
/**
 * Class For Working With Form Data 
 */
class FormObject extends ValuesObject
{
    /**
     * @var array List Of Form Data Values
     */
    public $data = [];

    /**
     * @var bool Status Of Processing Form
     */
    public $status = FALSE;

    /**
     * @var array List Of Errors
     */
    public $errors = [];

    /**
     * @var string Message For User
     */
    public $message = NULL;

    /**
    * Get Processing Form Status
    *
    * @return bool Processing Form Status Value
    */
    public function getStatus() : bool
    {
        return $this->status;
    }

    /**
     * Get Form Errors
     *
     * @return array List Of Errors
     */
    public function getErrors() : ?array
    {
        if ($this->getStatus()) {
            return NULL;
        }
        
        $errors = $this->errors;

        if (count($errors) < 1) {
            $errors[] = static::DEFAULT_ERROR_MESSAGE;
        }

        return $errors;
    }

    /**
     * Get Message For User
     *
     * @return string Message Value
     */
    public function getMessage() : ?string
    {
        return $this->message;
    }

    /**
     * Set Processing Form Status
     *
     * @param bool $status Processing Form Status Value
     */
    public function setStatus(bool $status = FALSE) : void
    {
        $this->status = $status;
    }

    /**
     * Set Processing Form Success Status
     */
    public function setSuccess() : void
    {
        $this->setStatus(TRUE);
    }

    /**
     * Set Processing Form Fail Status
     */
    public function setFail() : void
    {
        $this->setStatus(FALSE);
    }

    /**
     * Set Form Errors
     *
     * @param array $errors List Of Errors
     */
    public function setErrors(array $errors = []) : void
    {
        $this->errors = array_merge($this->errors, $errors);
        $this->errors = array_unique($this->errors);

        $this->setFail();
    }

    /**
     * Set Form Error
     *
     * @param array $error Error Message
     */
    public function setError(string $error = '') : void
    {
        $this->errors[] = $error;
        $this->errors = array_unique($this->errors);

        $this->setFail();
    }

    /**
     * Set Message For User
     *
     * @param array $message Message Value
     */
    public function setMessage(?string $message = NULL) : void
    {
        $this->message = $message;
    }
}
?>
