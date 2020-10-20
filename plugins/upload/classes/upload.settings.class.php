<?php
namespace Core\Plugins\Upload\Classes;

use Core\Plugins\Upload\Interfaces\IUploadSettings;

use Core\Plugins\Upload\Exceptions\UploadSettingsException;

class UploadSettings implements IUploadSettings
{
    const DEFAULT_FILE_SIZE = 1024 * 1024 * 2;

    const UPLOADS_DIR_PATH = __DIR__.'/../../../../res/uploads';

    private $_uploadsDir = null;

    private $_extensions = null;

    private $_maxSize = null;

    public function __construct(
        ?array  $extensions = null,
        ?int    $maxSize    = null,
        ?string $uploadsDir = null
    )
    {
        if (empty($uploadsDir)) {
            throw new UploadSettingsException(
                UploadSettingsException::MESSAGE_SETTINGS_DIR_PATH_IS_EMPTY,
                UploadSettingsException::CODE_SETTINGS_DIR_PATH_IS_EMPTY
            );
        }

        if (empty($extensions)) {
            throw new UploadSettingsException(
                UploadSettingsException::MESSAGE_SETTINGS_EXTENSIONS_IS_EMPTY,
                UploadSettingsException::CODE_SETTINGS_EXTENSIONS_IS_EMPTY
            );
        }

        if (empty($maxSize)) {
            $maxSize = static::DEFAULT_FILE_SIZE;
        }

        $this->_setUploadDirPath($uploadsDir);

        $this->_extensions = $extensions;
        $this->_maxSize    = $maxSize;
    }

    public function getUploadsDirPath(): string
    {
        if (empty($this->_uploadsDirPath)) {
            throw new UploadSettingsException(
                UploadSettingsException::MESSAGE_SETTINGS_DIR_PATH_IS_EMPTY,
                UploadSettingsException::CODE_SETTINGS_DIR_PATH_IS_EMPTY
            );
        }

        return $this->_uploadsDirPath;
    }

    public function getExtensions(): array
    {
        if (empty($this->_extensions)) {
            throw new UploadSettingsException(
                UploadSettingsException::MESSAGE_SETTINGS_EXTENSIONS_IS_EMPTY,
                UploadSettingsException::CODE_SETTINGS_EXTENSIONS_IS_EMPTY
            );
        }

        return $this->_extensions;
    }

    public function getMaxSize(): int
    {
        if (empty($this->_maxSize)) {
            return static::DEFAULT_FILE_SIZE;
        }

        return $this->_maxSize;
    }

    private function _setUploadDirPath(string $uploadsDir = null): void
    {
        $this->_uploadsDirPath = sprintf(
            '%s/%s/%s',
            static::UPLOADS_DIR_PATH,
            $uploadsDir,
            date('Y/m/d/H/i/s')
        );
    }
}
