<?php
namespace Core\Plugins\Upload\Classes;

use Core\Plugins\Upload\Interfaces\IUploadSaver;
use Core\Plugins\Upload\Interfaces\IUploadFile;

use Core\Plugins\Upload\Classes\UploadSettings;

use Core\Plugins\Upload\Exceptions\UploadSaverException;

class UploadSaver implements IUploadSaver
{
    private $_settings = null;

    public function __construct(
        ?array  $extensions = null,
        ?int    $maxSize    = null,
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
     * Save Files By Group
     *
     * @param array  $groupFiles Group Of Files
     *
     * @return array List Of Uploaded Files
     */
    public function saveByGroup(array $groupFiles = []): array
    {
        foreach ($groupFiles as $groupFilesKey => $file) {
            $groupFiles[$groupFilesKey] = $this->_saveFile($file);
        }

        return $groupFiles;
    }

    /**
     * Save Single File
     *
     * @param IUploadFile $file Input File Object
     *
     * @return string Uploaded File Path
     */
    private function _saveFile(IUploadFile $file): string
    {
        $this->_checkFile($file);

        $uploadedFilePath = $this->_getUploadedFilePath($file);

        if (!move_uploaded_file($file->getFilePath(), $uploadedFilePath)) {
            throw new UploadSaverException(
                UploadSaverException::MESSAGE_SAVER_FILE_SAVE_ERROR,
                UploadSaverException::CODE_SAVER_FILE_SAVE_ERROR
            );
        }

        chmod($uploadedFilePath, 0775);

        return realpath($uploadedFilePath);
    }

    /**
     * Get File Path Of Uploaded File
     *
     * @param IUploadFile $file Input File Object
     *
     * @return string Uploaded File Path
     */
    private function _getUploadedFilePath(IUploadFile $file): string
    {
        if (empty($this->_settings)) {
            throw new UploadSaverException(
                UploadSaverException::MESSAGE_SAVER_SETTINGS_ARE_NOT_SET,
                UploadSaverException::CODE_SAVER_SETTINGS_ARE_NOT_SET
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
            (string) $file->getExtension()
        );

        $fileUniqNumber = 0;

        while (file_exists($uploadedFilePath) && is_file($uploadedFilePath)) {
            $fileUniqNumber++;

            $uploadedFilePath = sprintf(
                '%s/%s-%d.%s',
                $uploadsDirPath,
                $file->getName(),
                $fileUniqNumber,
                (string) $file->getExtension()
            );
        }

        return $uploadedFilePath;
    }

    /**
     * Check Input File
     *
     * @param IUploadFile $file Input File Object
     */
    private function _checkFile(IUploadFile $file): void
    {
        if (empty($this->_settings)) {
            throw new UploadSaverException(
                UploadSaverException::MESSAGE_SAVER_SETTINGS_ARE_NOT_SET,
                UploadSaverException::CODE_SAVER_SETTINGS_ARE_NOT_SET
            );
        }

        if (
            UPLOAD_ERR_OK !== $file->getError() ||
            $file->getSize() < 1
        ) {
            throw new UploadSaverException(
                UploadSaverException::MESSAGE_SAVER_FILE_UPLOAD_ERROR,
                UploadSaverException::CODE_SAVER_FILE_UPLOAD_ERROR
            );
        }

        if ($file->getSize() > $this->_settings->getMaxSize()) {
            throw new UploadSaverException(
                UploadSaverException::MESSAGE_SAVER_FILE_TOO_LARGE,
                UploadSaverException::CODE_SAVER_FILE_TOO_LARGE
            );
        }

        if (
            empty($file->getExtension()) ||
            !in_array($file->getExtension(), $this->_settings->getExtensions())
        ) {
            throw new UploadSaverException(
                UploadSaverException::MESSAGE_SAVER_FILE_HAS_BAD_EXTENSION,
                UploadSaverException::CODE_SAVER_FILE_HAS_BAD_EXTENSION
            );
        }
    }
}
