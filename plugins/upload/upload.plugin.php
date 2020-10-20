<?php
use Core\Plugins\Upload\Interfaces\IUploadPlugin;
use Core\Plugins\Upload\Interfaces\IUploadFile;

use Core\Plugins\Upload\Classes\UploadSettings;
use Core\Plugins\Upload\Classes\UploadFile;

use Core\Plugins\Upload\Exceptions\UploadException;
use Core\Plugins\Upload\Exceptions\UploadPluginException;

/**
 * Plugin For Uploading Files
 */
class UploadPlugin implements IUploadPlugin
{
    private $_files = null;

    private $_settings = null;

    private $_error = null;

    public function __construct()
    {
        $this->_setFileObjects();
    }

    /**
     * Upload Files From Request To Server
     *
     * @param array|null  $extensions List Of Allowed Files Extensions
     * @param int|null    $maxSize    Max Allowed Size Of File (Bytes)
     * @param string|null $uploadsDir Path To Directory Of Uploaded Files
     *
     * @return array List Of Uploaded Files
     */
    public function upload
    (
        ?array  $extensions = null,
        ?int    $maxSize    = null,
        ?string $uploadsDir = null
    ): void
    {
        $this->_settings = new UploadSettings(
            $extensions,
            $maxSize,
            $uploadsDir
        );

        try {
            foreach ($this->_files as $groupKey => $groupFiles) {
                $this->_files[$groupKey] = $this->_uploadByGroup($groupFiles);
            } 
        } catch (UploadException $ext) {
            $this->_error = $ext->getMessage();
        }
    }

    public function getFiles(): ?array
    {
        if (!empty($this->_error)) {
            return null;
        }

        return $this->_files;
    }

    public function getError(): ?string
    {
        return $this->_error;
    }

    /**
     * Upload File Groups
     *
     * @param array  $groupFiles Group Of Files
     *
     * @return array List Of Uploaded Files
     */
    private function _uploadByGroup(array $groupFiles = []): array
    {
        foreach ($groupFiles as $groupFilesKey => $file) {
            $groupFiles[$groupFilesKey] = $this->_uploadFile($file);
        }

        return $groupFiles;
    }

    /**
     * Upload Single File
     *
     * @param IUploadFile $file Input File Object
     *
     * @return string Uploaded File Path
     */
    private function _uploadFile(IUploadFile $file): string
    {
        if (empty($this->_settings)) {
            throw new UploadPluginException(
                UploadPluginException::MESSAGE_PLUGIN_SETTINGS_ARE_NOT_SET,
                UploadPluginException::CODE_PLUGIN_SETTINGS_ARE_NOT_SET
            );
        }

        if (
            UPLOAD_ERR_OK !== $file->getError() ||
            $file->getSize() < 1
        ) {
            throw new UploadPluginException(
                UploadPluginException::MESSAGE_PLUGIN_FILE_UPLOAD_ERROR,
                UploadPluginException::CODE_PLUGIN_FILE_UPLOAD_ERROR
            );
        }

        if ($file->getSize() > $this->_settings->getMaxSize()) { 
            throw new UploadPluginException(
                UploadPluginException::MESSAGE_PLUGIN_FILE_TOO_LARGE,
                UploadPluginException::CODE_PLUGIN_FILE_TOO_LARGE
            );
        }

        if (
            empty($file->getExtension()) ||
            !in_array($file->getExtension(), $this->_settings->getExtensions())
        ) {
            throw new UploadPluginException(
                UploadPluginException::MESSAGE_PLUGIN_FILE_HAS_BAD_EXTENSION,
                UploadPluginException::CODE_PLUGIN_FILE_HAS_BAD_EXTENSION
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
            $file->getExtension()
        );

        $fileUniqNumber = 0;

        while (file_exists($uploadedFilePath) && is_file($uploadedFilePath)) {
            $fileUniqNumber++;

            $uploadedFilePath = sprintf(
                '%s/%s-%d.%s',
                $uploadsDirPath,
                $file->getName(),
                $fileUniqNumber,
                $file->getExtension()
            );
        }

        if (!move_uploaded_file($file->getFilePath(), $uploadedFilePath)) {
            throw new UploadPluginException(
                UploadPluginException::MESSAGE_PLUGIN_FILE_SAVE_ERROR,
                UploadPluginException::CODE_PLUGIN_FILE_SAVE_ERROR
            );
        }

        chmod($uploadedFilePath, 0775);

        return realpath($uploadedFilePath);
    }

    private function _setFileObjects()
    {
        $this->_files = [];

        $this->_mapSingleFiles();
        $this->_mapMultipleFiles();

        foreach ($this->_files as $groupName => $groupFiles) {
            $groupFiles = $this->_setGroupFileObjects($groupFiles);

            if (empty($groupFiles)) {
                unset($this->_files[$groupName]);
                continue;
            }

            $this->_files[$groupName] = $groupFiles;
        }
    }

    private function _setGroupFileObjects(?array $groupFiles = null): ?array
    {
        if (empty($groupFiles)) {
            return null;
        }

        foreach ($groupFiles as $fileKey => $file) {
            if (
                !array_key_exists('name', $file) ||
                !array_key_exists('tmp_name', $file) ||
                !array_key_exists('size', $file) ||
                !array_key_exists('error', $file)
            ) {
                throw new UploadPluginException(
                    UploadPluginException::MESSAGE_PLUGIN_FILE_HAS_BAD_FORMAT,
                    UploadPluginException::CODE_PLUGIN_FILE_HAS_BAD_FORMAT
                );
            }

            if (
                empty($file['name']) ||
                empty($file['tmp_name'])
            ) {
                unset($groupFiles[$fileKey]);
                continue; 
            }

            $groupFiles[$fileKey] = new UploadFile(
                $file['name'],
                $file['tmp_name'],
                $file['size'],
                $file['error']
            );
        }

        return $groupFiles;
    }

    /**
     * Map Files That Uploaded In Single Mode From $_FILES To $this->files
     */
    private function _mapSingleFiles(): void
    {
        foreach ($_FILES as $fileName => $file) {
            if (
                !array_key_exists('name', $file) ||
                !is_array($file['name'])
            ) {
                $this->_files[$fileName] = [$file];
                unset($_FILES[$fileName]);
            }
        }
    }

    /**
     * Map File Sets That Uploaded In Multiple Mode From $_FILES
     * To $this->files
     */
    private function _mapMultipleFiles(): void
    {
        foreach ($_FILES as $groupName => $multipleFile) {
            $this->_files[$groupName] = $this->_mapMultipleFileSet(
                $multipleFile
            );

            unset($_FILES[$groupName]);
        }
    }

    /**
     * Map Files That Uploaded In Multiple Mode
     */
    private function _mapMultipleFileSet(array $multipleFile): array
    {
        $mappedFiles = [];

        foreach ($multipleFile as $valuesName => $valuesSet) {
            $mappedFiles = $this->_mapMultipleFileValue(
                $mappedFiles,
                $valuesName,
                $valuesSet
            );
        }

        return $mappedFiles;
    }

    /**
     * Map Values To Files That Uploaded In Multiple Mode
     */
    private function _mapMultipleFileValue(
        array  $mappedFiles,
        string $valuesName,
        array  $valuesSet
    ): array
    {
        foreach ($valuesSet as $valuesKey => $value) {
            if (!array_key_exists($valuesKey, $mappedFiles)) {
                $mappedFiles[$valuesKey] = [];
            }

            $mappedFiles[$valuesKey][$valuesName] = $value;
        }

        return $mappedFiles;
    }
}
