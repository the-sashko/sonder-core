<?php
namespace Core\Plugins\Image\Classes;

use Core\Plugins\Image\Interfaces\IImageSize;

use Core\Plugins\Image\Exceptions\ImagePluginException;

class ImageSize implements IImageSize
{
    const PREFIX_FULL = 'full';

    private $_height = null;

    private $_width = null;

    private $_filePrefix = null;

    public function __construct(?array $values = null)
    {
        if (empty($values)) {
            throw new ImageSizeException(
                ImageSizeException::MESSAGE_SIZE_VALUES_NOT_SET,
                ImageSizeException::CODE_SIZE_VALUES_NOT_SET
            );
        }

        if (!$this->_isValuesCorectFormat($values)) {
            throw new ImageSizeException(
                ImageSizeException::MESSAGE_SIZE_VALUES_HAS_BAD_FORMAT,
                ImageSizeException::CODE_SIZE_VALUES_HAS_BAD_FORMAT
            );
        }

        $this->_height     = (int)    $values['height'];
        $this->_width      = (int)    $values['width'];
        $this->_filePrefix = (string) $values['file_prefix'];
    }

    public function getHeight(): int
    {
        return $this->_height;
    }

    public function getWidth(): int
    {
        return $this->_width;
    }

    public function getFilePrefix(): string
    {
        return $this->_filePrefix;
    }

    public function setHeight(?int $height = null): void
    {
        if (empty($height)) {
            throw new ImageSizeException(
                ImageSizeException::MESSAGE_SIZE_HEIGHT_VALUE_IS_EMPTY,
                ImageSizeException::CODE_SIZE_HEIGHT_VALUE_IS_EMPTY
            );
        }

        $this->_height = $height;
    }

    public function setWidth(?int $width = null): void
    {
        if (empty($width)) {
            throw new ImageSizeException(
                ImageSizeException::MESSAGE_SIZE_WIDTH_VALUE_IS_EMPTY,
                ImageSizeException::CODE_SIZE_WIDTH_VALUE_IS_EMPTY
            );
        }

        $this->_width = $width;
    }

    private function _isValuesCorectFormat(?array $values = null): bool
    {
        if (empty($values)) {
            return false;
        }

        if (
            !array_key_exists('height', $values) ||
            !array_key_exists('width', $values) ||
            !array_key_exists('file_prefix', $values)
        ) {
            return false;
        }

        if ($values['file_prefix'] == static::PREFIX_FULL) {
            throw new ImagePluginException(
                ImagePluginException::MESSAGE_SIZE_FULL_PREFIX_NOT_ALLOWED,
                ImagePluginException::CODE_SIZE_FULL_PREFIX_NOT_ALLOWED
            );
        }

        return (!empty($values['width']) || !empty($values['height'])) &&
               !empty($values['file_prefix']);
    }
}
