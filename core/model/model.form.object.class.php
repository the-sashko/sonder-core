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
     * @param string|null $fileName
     * @return array|null
     */
    final protected function getFileValueFromRequest(
        ?string $fileName = null
    ): ?array
    {
        if (
            empty($_FILES) ||
            !array_key_exists($fileName, $_FILES) &&
            empty($_FILES[$fileName])
        ) {
            return null;
        }

        $name = null;
        $extension = null;
        $size = null;
        $path = null;
        $error = false;

        if (array_key_exists('name', $_FILES[$fileName])) {
            $name = $_FILES[$fileName]['name'];
        }

        if (!empty($fileName)) {
            $extension = explode('.', $name);
            $extension = end($extension);
            $extension = mb_convert_case($extension, MB_CASE_LOWER);
        }

        if ($extension == 'jpeg') {
            $extension = 'jpg';
        }

        if (array_key_exists('size', $_FILES[$fileName])) {
            $size = (int)$_FILES[$fileName]['size'];
        }

        if (array_key_exists('tmp_name', $_FILES[$fileName])) {
            $path = (string)$_FILES[$fileName]['tmp_name'];
        }

        if (array_key_exists('error', $_FILES[$fileName])) {
            $error = (bool)$_FILES[$fileName]['error'];
        }

        if (empty($name) || empty($size) || empty($path)) {
            return null;
        }

        return [
            'name' => $name,
            'extension' => $extension,
            'size' => $size,
            'path' => $path,
            'error' => $error
        ];
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
