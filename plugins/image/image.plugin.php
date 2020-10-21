<?php
use Core\Plugins\Image\Interfaces\IImagePlugin;

use Core\Plugins\Image\Classes\ImageSize;

use Core\Plugins\Image\Exceptions\ImagePluginException;

/**
 * Plugin For Resizing Images
 */
class ImagePlugin implements IImagePlugin
{
    const DEFAULT_IMAGE_FORMAT = 'png';

    const DEFAULT_FILE_NAME = 'image';

    const DEFAULT_SIZE_VALUES = [
        'thumbnail' => [
            'height'      => 64,
            'width'       => 64,
            'file_prefix' => 'thumb'
        ],
        'small' => [
            'height'      => null,
            'width'       => 128,
            'file_prefix' => 's'
        ],
        'middle' => [
            'height'      => null,
            'width'       => 246,
            'file_prefix' => 'm'
        ],
        'large' => [
            'height'      => null,
            'width'       => 200,
            'file_prefix' => 'l'
        ]
    ];

    private $_imageFormat = null;

    private $_imageFilePath = null;

    private $_imageDirPath = null;

    private $_imageFileName = null;

    public function resize(
        ?string $imageFilePath = null,
        ?string $imageDirPath  = null,
        ?string $imageFileName = null,
        ?string $imageFormat   = null,
        ?array  $sizes         = null
    ): void
    {
        if (empty($imageFilePath)) {
            throw new ImagePluginException(
                ImagePluginException::MESSAGE_PLUGIN_FILE_PATH_IS_NOT_SET,
                ImagePluginException::CODE_PLUGIN_FILE_PATH_IS_NOT_SET
            );
        }

        if (empty($imageDirPath)) {
            throw new ImagePluginException(
                ImagePluginException::MESSAGE_PLUGIN_DIR_PATH_IS_NOT_SET,
                ImagePluginException::CODE_PLUGIN_DIR_PATH_IS_NOT_SET
            );
        }

        if (empty($imageFileName)) {
            throw new ImagePluginException(
                ImagePluginException::MESSAGE_PLUGIN_FILE_NAME_IS_NOT_SET,
                ImagePluginException::CODE_PLUGIN_FILE_NAME_IS_NOT_SET
            );
        }

        if (empty($imageFormat)) {
            $imageFormat = static::DEFAULT_IMAGE_FORMAT;
        }

        if (empty($sizes)) {
            $sizes = static::DEFAULT_SIZE_VALUES;
        }

        $this->_imageFilePath = $imageFilePath;
        $this->_imageDirPath  = $imageDirPath;
        $this->_imageFileName = $imageFileName;
        $this->_imageFormat   = $imageFormat;

        $this->_saveOriginalSize();

        foreach ($sizes as $size) {
            $size = new ImageSize($size);

            $this->_imageResize($size);
        }
    }

    private function _saveOriginalSize(): void
    {
        $imageObject = $this->_getImageObject();

        $this->_saveImage($imageObject, ImageSize::PREFIX_FULL);

        $imageObject->clear();
        $imageObject->destroy();
    }

    private function _imageResize(?ImageSize $size = null): void
    {
        if (empty($size)) {
            throw new ImagePluginException(
                ImagePluginException::MESSAGE_PLUGIN_SIZE_OBJECT_IS_EMPTY,
                ImagePluginException::CODE_PLUGIN_SIZE_OBJECT_IS_EMPTY
            );
        }

        $imageObject = $this->_getImageObject();

        $imageGeometry  = $imageObject->getImageGeometry();
        $originalWidth  = (int) $imageGeometry['width'];
        $originalHeight = (int) $imageGeometry['height'];

        if (empty($size->getWidth()) || empty($size->getHeight())) {
            $size = $this->_calculateSize(
                $size,
                $originalWidth,
                $originalHeight
            );
        }

        if (!empty($size->getWidth()) && !empty($size->getHeight())) {
            $imageObject = $this->_changeImageRatio(
                $imageObject,
                $size,
                $originalWidth,
                $originalHeight
            );
        }

        $imageObject->resizeImage(
            $size->getWidth(),
            $size->getHeight(),
            Imagick::FILTER_LANCZOS,
            1
        );

        $this->_saveImage($imageObject, $size->getFilePrefix());

        $imageObject->clear();
        $imageObject->destroy();
    }

    private function _getImageObject(): \Imagick
    {
        $imageObject = new \Imagick();
        $imageObject->readImage($this->_imageFilePath);
        $imageObject->setImageFormat($this->_imageFormat);

        return $imageObject;
    }

    private function _calculateSize(
        ?ImageSize $size           = null,
        ?int       $originalWidth  = null,
        ?int       $originalHeight = null
    ): ImageSize
    {
        if (empty($size)) {
            throw new ImagePluginException(
                ImagePluginException::MESSAGE_PLUGIN_SIZE_OBJECT_IS_EMPTY,
                ImagePluginException::CODE_PLUGIN_SIZE_OBJECT_IS_EMPTY
            );
        }

        if (empty($originalWidth)) {
            throw new ImagePluginException(
                ImagePluginException::MESSAGE_PLUGIN_ORIGINAL_WIDTH_IS_EMPTY,
                ImagePluginException::CODE_PLUGIN_ORIGINAL_WIDTH_IS_EMPTY
            );
        }

        if (empty($originalHeight)) {
            throw new ImagePluginException(
                ImagePluginException::MESSAGE_PLUGIN_ORIGINAL_HEIGHT_IS_EMPTY,
                ImagePluginException::CODE_PLUGIN_ORIGINAL_HEIGHT_IS_EMPTY
            );
        }

        if (empty($size->getWidth())) {
            $size->setWidth(
                (int) (($originalWidth / $originalHeight) * $size->getheight())
            );

            return $size;
        }

        $size->setHeight(
            (int) (($originalHeight / $originalWidth) * $size->getWidth())
        );

        return $size;
    }

    private function _changeImageRatio(
        \Imagick   $imageObject    = null,
        ?ImageSize $size           = null,
        ?int       $originalWidth  = null,
        ?int       $originalHeight = null
    ): \Imagick
    {
        if (empty($imageObject)) {
            throw new ImagePluginException(
                ImagePluginException::MESSAGE_PLUGIN_IMAGE_OBJECT_IS_EMPTY,
                ImagePluginException::CODE_PLUGIN_IMAGE_OBJECT_IS_EMPTY
            );
        }

        if (empty($size)) {
            throw new ImagePluginException(
                ImagePluginException::MESSAGE_PLUGIN_SIZE_OBJECT_IS_EMPTY,
                ImagePluginException::CODE_PLUGIN_SIZE_OBJECT_IS_EMPTY
            );
        }

        if (empty($originalWidth)) {
            throw new ImagePluginException(
                ImagePluginException::MESSAGE_PLUGIN_ORIGINAL_WIDTH_IS_EMPTY,
                ImagePluginException::CODE_PLUGIN_ORIGINAL_WIDTH_IS_EMPTY
            );
        }

        if (empty($originalHeight)) {
            throw new ImagePluginException(
                ImagePluginException::MESSAGE_PLUGIN_ORIGINAL_HEIGHT_IS_EMPTY,
                ImagePluginException::CODE_PLUGIN_ORIGINAL_HEIGHT_IS_EMPTY
            );
        }

        if (empty($size->getWidth()) && empty($size->getHeight())) {
            throw new Exception("Image Size \"{$size}\" Have Bad Format");
        }

        $newWidth  = $originalWidth;

        $newHeight = (int) (
            ($size->getHeight() / $size->getWidth()) * $newWidth
        );

        if ($newHeight > $originalHeight) {
            $newHeight = $originalHeight;

            $newWidth  = (int) (
                ($size->getWidth() / $size->getHeight()) * $newHeight
            );
        }

        $positionX = 0;
        $positionY = 0;

        if ($newWidth < $originalWidth) {
            $positionX = (int) (($originalWidth-$newWidth) / 2);
        }

        if ($newHeight < $originalHeight) {
            $positionY = (int) (($originalHeight-$newHeight) / 2);
        }

        $positionX = $positionX > 0 ? $positionX + 1 : 0;
        $positionY = $positionY > 0 ? $positionY + 1 : 0;

        $imageObject->cropImage($newWidth, $newHeight, $positionX, $positionY);

        return $imageObject;
    }

    private function _saveImage(
        \Imagick $imageObject = null,
        ?string  $filePrefix  = null
    ): void
    {
        if (empty($imageObject)) {
            throw new ImagePluginException(
                ImagePluginException::MESSAGE_PLUGIN_IMAGE_OBJECT_IS_EMPTY,
                ImagePluginException::CODE_PLUGIN_IMAGE_OBJECT_IS_EMPTY
            );
        }

        if (empty($filePrefix)) {
            throw new ImagePluginException(
                ImagePluginException::MESSAGE_PLUGIN_FILE_PREFIX_IS_EMPTY,
                ImagePluginException::CODE_PLUGIN_FILE_PREFIX_IS_EMPTY
            );
        }

        $imageObject->setImageCompressionQuality(90);

        $imageFilePath = $this->_getImageFilePath($filePrefix);

        if (file_exists($imageFilePath) && is_file($imageFilePath)) {
            $errorMessage = sprintf(
                '%s. File: %s-%s.%s',
                ImagePluginException::MESSAGE_PLUGIN_FILE_ALREADY_EXISTS,
                $this->_imageFileName,
                $filePrefix,
                $this->_imageFormat
            );

            throw new ImagePluginException(
                $errorMessage,
                ImagePluginException::CODE_PLUGIN_FILE_ALREADY_EXISTS
            );
        }

        $imageObject->setImageCompression(Imagick::COMPRESSION_ZIP);
        $imageObject->writeImage($imageFilePath);
        chmod($imageFilePath, 0775);
    }

    private function _getImageFilePath(?string $filePrefix = null): string
    {
        if (empty($filePrefix)) {
            throw new ImagePluginException(
                ImagePluginException::MESSAGE_PLUGIN_FILE_PREFIX_IS_EMPTY,
                ImagePluginException::CODE_PLUGIN_FILE_PREFIX_IS_EMPTY
            );
        }

        if (
            !file_exists($this->_imageDirPath) ||
            !is_dir($this->_imageDirPath)
        ) {
            mkdir($this->_imageDirPath, 0775, true);
        }

        return sprintf(
            '%s/%s-%s.%s',
            $this->_imageDirPath,
            $this->_imageFileName,
            $filePrefix,
            $this->_imageFormat
        );
    }
}
