<?php

namespace Sonder\Plugins;

use Sonder\Plugins\Upload\Classes\UploadFile;
use Sonder\Plugins\Upload\Classes\UploadSaver;
use Sonder\Plugins\Upload\Exceptions\UploadException;
use Sonder\Plugins\Upload\Exceptions\UploadFileException;
use Sonder\Plugins\Upload\Exceptions\UploadPluginException;
use Sonder\Plugins\Upload\Interfaces\IUploadPlugin;

final class UploadPlugin implements IUploadPlugin
{
    /**
     * @var array|null
     */
    private ?array $_files = null;

    /**
     * @var string|null
     */
    private ?string $_error = null;

    /**
     * @throws UploadPluginException
     * @throws UploadFileException
     */
    final public function __construct()
    {
        if (!$this->_setFileObjects()) {
            $this->_files = null;
        }
    }

    /**
     * @param array|null $extensions
     * @param int|null $maxSize
     * @param string|null $uploadsDir
     */
    final public function upload(
        ?array  $extensions = null,
        ?int    $maxSize = null,
        ?string $uploadsDir = null
    ): void
    {
        try {
            $saver = new UploadSaver($extensions, $maxSize, $uploadsDir);

            foreach ($this->_files as $groupKey => $groupFiles) {
                $this->_files[$groupKey] = $saver->saveByGroup($groupFiles);
            }
        } catch (UploadException $ext) {
            $this->_error = $ext->getMessage();
        }
    }

    /**
     * @return array|null
     */
    final public function getFiles(): ?array
    {
        if (!empty($this->_error)) {
            return null;
        }

        return $this->_files;
    }

    /**
     * @return string|null
     */
    final public function getError(): ?string
    {
        return $this->_error;
    }

    /**
     * @return bool
     *
     * @throws UploadPluginException
     * @throws Upload\Exceptions\UploadFileException
     */
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

    /**
     * @param array|null $groupFiles
     *
     * @return array|null
     *
     * @throws UploadPluginException
     * @throws Upload\Exceptions\UploadFileException
     */
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
                    UploadException::CODE_PLUGIN_FILE_HAS_BAD_FORMAT
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
     * @param array $multipleFile
     *
     * @return array
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
     * @param array $mappedFiles
     * @param string $valuesName
     * @param array $valuesSet
     *
     * @return array
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
