<?php

namespace Sonder\Core;

use Sonder\Interfaces\IModelFormFileObject;
use Sonder\Interfaces\IModelFormObject;
use Sonder\Interfaces\IValuesObject;

#[IValuesObject]
#[IModelFormObject]
abstract class ModelFormObject extends ValuesObject implements IModelFormObject
{
    final protected const DEFAULT_ERROR_MESSAGE = 'Unknown Error';

    /**
     * @var bool
     */
    public bool $status = false;

    /**
     * @var array
     */
    public array $errors = [];

    /**
     * @return void
     */
    abstract protected function checkInputValues(): void;

    /**
     * @param array|null $values
     */
    public function __construct(?array $values = null)
    {
        parent::__construct($values);

        if (!empty($values)) {
            $this->checkInputValues();
        }
    }

    /**
     * @return bool
     */
    final public function getStatus(): bool
    {
        return $this->status;
    }

    /**
     * @return array|string[]|null
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

    /**
     * @return void
     */
    final public function setStatusSuccess(): void
    {
        $this->_setStatus(true);
    }

    /**
     * @return void
     */
    final public function setStatusFail(): void
    {
        $this->_setStatus();
    }

    /**
     * @param array|null $errors
     * @return void
     */
    final public function setErrors(?array $errors = null): void
    {
        $this->errors = array_merge($this->errors, (array)$errors);
        $this->errors = array_unique($this->errors);

        $this->setStatusFail();
    }

    /**
     * @param string|null $error
     * @return void
     */
    final public function setError(?string $error = null): void
    {
        $this->errors[] = $error;
        $this->errors = array_unique($this->errors);

        $this->setStatusFail();
    }

    /**
     * @param string|null $fileName
     * @return IModelFormFileObject|null
     */
    final protected function getFileValueFromRequest(
        ?string $fileName = null
    ): ?IModelFormFileObject {
        if (empty($fileName) || empty($_FILES) || !isset($fileName, $_FILES)) {
            return null;
        }

        $modelFormFileObject = new ModelFormFileObject($fileName);

        if (
            empty($modelFormFileObject->getName()) ||
            empty($modelFormFileObject->getSize()) ||
            empty($modelFormFileObject->getPath())
        ) {
            return null;
        }

        return $modelFormFileObject;
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
     * @return void
     */
    private function _setStatus(bool $status = false): void
    {
        $this->status = $status;
    }
}
