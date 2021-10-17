<?php

namespace Sonder\Plugins\Upload\Classes;

use Sonder\Plugins\Upload\Exceptions\UploadException;
use Sonder\Plugins\Upload\Exceptions\UploadSaverException;
use Sonder\Plugins\Upload\Exceptions\UploadSettingsException;
use Sonder\Plugins\Upload\Interfaces\IUploadFile;
use Sonder\Plugins\Upload\Interfaces\IUploadSaver;

final class UploadSaver implements IUploadSaver
{
    /**
     * @var UploadSettings
     */
    private UploadSettings $_settings;

    /**
     * @param array|null $extensions
     * @param int|null $maxSize
     * @param string|null $uploadsDir
     *
     * @throws UploadSettingsException
     */
    final public function __construct(
        ?array  $extensions = null,
        ?int    $maxSize = null,
        ?string $uploadsDir = null
    )
    {
        $this->_settings = new UploadSettings(
            $extensions,
            $maxSize,
            $uploadsDir
        );
    }

    /**
     * @param array $groupFiles
     *
     * @return array
     *
     * @throws UploadSaverException
     * @throws UploadSettingsException
     */
    final public function saveByGroup(array $groupFiles = []): array
    {
        foreach ($groupFiles as $groupFilesKey => $file) {
            $groupFiles[$groupFilesKey] = $this->_saveFile($file);
        }

        return $groupFiles;
    }

    /**
     * @param IUploadFile $file
     *
     * @return string
     *
     * @throws UploadSaverException
     * @throws UploadSettingsException
     */
    private function _saveFile(IUploadFile $file): string
    {
        $this->_checkFile($file);

        $uploadedFilePath = $this->_getUploadedFilePath($file);

        if (!move_uploaded_file($file->getFilePath(), $uploadedFilePath)) {
            throw new UploadSaverException(
                UploadSaverException::MESSAGE_SAVER_FILE_SAVE_ERROR,
                UploadException::CODE_SAVER_FILE_SAVE_ERROR
            );
        }

        chmod($uploadedFilePath, 0775);

        return realpath($uploadedFilePath);
    }

    /**
     * @param IUploadFile $file
     *
     * @return string
     *
     * @throws UploadSaverException
     * @throws UploadSettingsException
     */
    private function _getUploadedFilePath(IUploadFile $file): string
    {
        if (empty($this->_settings)) {
            throw new UploadSaverException(
                UploadSaverException::MESSAGE_SAVER_SETTINGS_ARE_NOT_SET,
                UploadException::CODE_SAVER_SETTINGS_ARE_NOT_SET
            );
        }

        $uploadsDirPath = $this->_settings->getUploadsDirPath();

        if (
            !file_exists($uploadsDirPath) ||
            !is_dir($uploadsDirPath)
        ) {
            mkdir($uploadsDirPath, 0775, true);
        }

        $uploadedFilePath = sprintf(
            '%s/%s.%s',
            $uploadsDirPath,
            $file->getName(),
            (string)$file->getExtension()
        );

        $fileUniqNumber = 0;

        while (file_exists($uploadedFilePath) && is_file($uploadedFilePath)) {
            $fileUniqNumber++;

            $uploadedFilePath = sprintf(
                '%s/%s-%d.%s',
                $uploadsDirPath,
                $file->getName(),
                $fileUniqNumber,
                (string)$file->getExtension()
            );
        }

        return $uploadedFilePath;
    }

    /**
     * @param IUploadFile $file
     *
     * @throws UploadSaverException
     * @throws UploadSettingsException
     */
    private function _checkFile(IUploadFile $file): void
    {
        if (empty($this->_settings)) {
            throw new UploadSaverException(
                UploadSaverException::MESSAGE_SAVER_SETTINGS_ARE_NOT_SET,
                UploadException::CODE_SAVER_SETTINGS_ARE_NOT_SET
            );
        }

        if (
            UPLOAD_ERR_OK !== $file->getError() ||
            $file->getSize() < 1
        ) {
            throw new UploadSaverException(
                UploadSaverException::MESSAGE_SAVER_FILE_UPLOAD_ERROR,
                UploadException::CODE_SAVER_FILE_UPLOAD_ERROR
            );
        }

        if ($file->getSize() > $this->_settings->getMaxSize()) {
            throw new UploadSaverException(
                UploadSaverException::MESSAGE_SAVER_FILE_TOO_LARGE,
                UploadException::CODE_SAVER_FILE_TOO_LARGE
            );
        }

        if (
            empty($file->getExtension()) ||
            !in_array($file->getExtension(), $this->_settings->getExtensions())
        ) {
            throw new UploadSaverException(
                UploadSaverException::MESSAGE_SAVER_FILE_HAS_BAD_EXTENSION,
                UploadException::CODE_SAVER_FILE_HAS_BAD_EXTENSION
            );
        }
    }
}
