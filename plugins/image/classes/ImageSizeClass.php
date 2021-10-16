<?php

namespace Sonder\Plugins\Image\Classes;

use Sonder\Plugins\Image\Exceptions\ImageException;
use Sonder\Plugins\Image\Exceptions\ImageSizeException;
use Sonder\Plugins\Image\Interfaces\IImageSize;

final class ImageSize implements IImageSize
{
    const PREFIX_FULL = 'full';

    /**
     * @var int|null
     */
    private ?int $_height = null;

    /**
     * @var int|null
     */
    private ?int $_width = null;

    /**
     * @var string|null
     */
    private ?string $_filePrefix = null;

    /**
     * @param array|null $values
     *
     * @throws ImageSizeException
     */
    final public function __construct(?array $values = null)
    {
        if (empty($values)) {
            throw new ImageSizeException(
                ImageSizeException::MESSAGE_SIZE_VALUES_NOT_SET,
                ImageException::CODE_SIZE_VALUES_NOT_SET
            );
        }

        if (!$this->_isValuesHasCorrectFormat($values)) {
            throw new ImageSizeException(
                ImageSizeException::MESSAGE_SIZE_VALUES_HAS_BAD_FORMAT,
                ImageException::CODE_SIZE_VALUES_HAS_BAD_FORMAT
            );
        }

        $this->_height = (int)$values['height'];
        $this->_width = (int)$values['width'];
        $this->_filePrefix = (string)$values['file_prefix'];
    }

    /**
     * @return int
     */
    final public function getHeight(): int
    {
        return $this->_height;
    }

    /**
     * @return int
     */
    final public function getWidth(): int
    {
        return $this->_width;
    }

    /**
     * @return string
     */
    final public function getFilePrefix(): string
    {
        return $this->_filePrefix;
    }

    /**
     * @param int|null $height
     *
     * @throws ImageSizeException
     */
    final public function setHeight(?int $height = null): void
    {
        if (empty($height)) {
            throw new ImageSizeException(
                ImageSizeException::MESSAGE_SIZE_HEIGHT_VALUE_IS_EMPTY,
                ImageException::CODE_SIZE_HEIGHT_VALUE_IS_EMPTY
            );
        }

        $this->_height = $height;
    }

    /**
     * @param int|null $width
     *
     * @throws ImageSizeException
     */
    final public function setWidth(?int $width = null): void
    {
        if (empty($width)) {
            throw new ImageSizeException(
                ImageSizeException::MESSAGE_SIZE_WIDTH_VALUE_IS_EMPTY,
                ImageException::CODE_SIZE_WIDTH_VALUE_IS_EMPTY
            );
        }

        $this->_width = $width;
    }

    /**
     * @param array|null $values
     *
     * @return bool
     *
     * @throws ImageSizeException
     */
    private function _isValuesHasCorrectFormat(?array $values = null): bool
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

        if ($values['file_prefix'] == ImageSize::PREFIX_FULL) {
            throw new ImageSizeException(
                ImageSizeException::MESSAGE_SIZE_FULL_PREFIX_NOT_ALLOWED,
                ImageException::CODE_SIZE_FULL_PREFIX_NOT_ALLOWED
            );
        }

        return (!empty($values['width']) || !empty($values['height'])) &&
            !empty($values['file_prefix']);
    }
}
