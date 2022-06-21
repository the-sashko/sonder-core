<?php

namespace Sonder\Core;

use Sonder\Interfaces\IModelFormFileObject;

#[IModelFormFileObject]
class ModelFormFileObject implements IModelFormFileObject
{
    /**
     * @var string|null
     */
    private ?string $_name = null;

    /**
     * @var string|null
     */
    private ?string $_extension = null;

    /**
     * @var int|null
     */
    private ?int $_size = null;

    /**
     * @var string|null
     */
    private ?string $_path = null;

    /**
     * @var bool
     */
    private bool $_error = false;

    /**
     * @param string $fileName
     */
    public function __construct(string $fileName)
    {
        $name = $_FILES[$fileName]['name'] ?? null;
        $size = $_FILES[$fileName]['size'] ?? null;
        $path = $_FILES[$fileName]['tmp_name'] ?? null;
        $error = $_FILES[$fileName]['error'] ?? false;

        if (!empty($name) && !empty($size) && !empty($path)) {
            $extension = $this->_extractExtension($name);

            $this
                ->_setName($name)
                ->_setExtension($extension)
                ->_setSize((int)$size)
                ->_setPath((string)$path)
                ->_setError((bool)$error);
        }
    }

    /**
     * @return string|null
     */
    final public function getName(): ?string
    {
        return empty($this->_name) ? null : $this->_name;
    }

    /**
     * @return string|null
     */
    final public function getExtension(): ?string
    {
        return empty($this->_extension) ? null : $this->_extension;
    }

    /**
     * @return int|null
     */
    final public function getSize(): ?int
    {
        return empty($this->_size) ? null : $this->_size;
    }

    /**
     * @return string|null
     */
    final public function getPath(): ?string
    {
        return empty($this->_path) ? null : $this->_path;
    }

    /**
     * @return bool
     */
    final public function getError(): bool
    {
        return $this->_error;
    }

    /**
     * @param string $name
     * @return $this
     */
    private function _setName(string $name): static
    {
        $this->_name = $name;

        return $this;
    }

    /**
     * @param string $extension
     * @return $this
     */
    private function _setExtension(string $extension): static
    {
        $this->_extension = $extension;

        return $this;
    }

    /**
     * @param int $size
     * @return $this
     */
    private function _setSize(int $size): static
    {
        $this->_size = $size;

        return $this;
    }

    /**
     * @param string $path
     * @return $this
     */
    private function _setPath(string $path): static
    {
        $this->_path = $path;

        return $this;
    }

    /**
     * @param bool $error
     * @return $this
     */
    private function _setError(bool $error): static
    {
        $this->_error = $error;

        return $this;
    }

    /**
     * @param string $fileName
     * @return string
     */
    private function _extractExtension(string $fileName): string
    {
        $extension = explode('.', $fileName);
        $extension = end($extension);
        $extension = mb_convert_case($extension, MB_CASE_LOWER);

        if ($extension == 'jpeg') {
            $extension = 'jpg';
        }

        return $extension;
    }
}
