<?php
use Core\Plugins\Upload\Interfaces\IUploadPlugin;

use Core\Plugins\Upload\Classes\UploadFile;
use Core\Plugins\Upload\Classes\UploadSaver;

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
     */
    public function upload(
        ?array  $extensions = null,
        ?int    $maxSize    = null,
        ?string $uploadsDir = null
    ): void
    {
        try {
            foreach ($this->_files as $groupKey => $groupFiles) {
                $this->_files[$groupKey] = (new UploadSaver)->saveByGroup(
                    $groupFiles
                );
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

    private function _setFileObjects(): bool
    {
        $this->_files = [];

        $this->_mapFiles();

        if (empty($this->_files)) {
            return false;
        }

        foreach ($this->_files as $groupName => $groupFiles) {
            $groupFiles = $this->_setGroupFileObjects($groupFiles);

            if (empty($groupFiles)) {
                unset($this->_files[$groupName]);
                continue;
            }

            $this->_files[$groupName] = $groupFiles;
        }

        return true;
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
     * Map File Sets That Uploaded In Multiple Mode From $_FILES
     * To $this->files
     */
    private function _mapFiles(): void
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
