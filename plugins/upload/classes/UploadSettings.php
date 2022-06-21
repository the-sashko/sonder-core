<?php

namespace Sonder\Plugins\Upload\Classes;

use Sonder\Plugins\Upload\Exceptions\UploadException;
use Sonder\Plugins\Upload\Exceptions\UploadSettingsException;
use Sonder\Plugins\Upload\Interfaces\IUploadSettings;

final class UploadSettings implements IUploadSettings
{
    private const DEFAULT_FILE_SIZE = 1024 * 1024 * 2;

    private const PROTECTED_DIR_PATH = __DIR__ . '/../../../..';

    /**
     * @var string|null
     */
    private ?string $_uploadsDirPath = null;

    /**
     * @var array|null
     */
    private ?array $_extensions = null;

    /**
     * @var int|null
     */
    private ?int $_maxSize = null;

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
        if (empty($uploadsDir)) {
            throw new UploadSettingsException(
                UploadSettingsException::MESSAGE_SETTINGS_DIR_PATH_IS_EMPTY,
                UploadException::CODE_SETTINGS_DIR_PATH_IS_EMPTY
            );
        }

        if (empty($extensions)) {
            throw new UploadSettingsException(
                UploadSettingsException::MESSAGE_SETTINGS_EXTENSIONS_IS_EMPTY,
                UploadException::CODE_SETTINGS_EXTENSIONS_IS_EMPTY
            );
        }

        if (empty($maxSize)) {
            $maxSize = UploadSettings::DEFAULT_FILE_SIZE;
        }

        $this->_setUploadDirPath($uploadsDir);

        $this->_extensions = $extensions;
        $this->_maxSize = $maxSize;
    }

    /**
     * @return string
     *
     * @throws UploadSettingsException
     */
    final public function getUploadsDirPath(): string
    {
        if (empty($this->_uploadsDirPath)) {
            throw new UploadSettingsException(
                UploadSettingsException::MESSAGE_SETTINGS_DIR_PATH_IS_EMPTY,
                UploadException::CODE_SETTINGS_DIR_PATH_IS_EMPTY
            );
        }

        return $this->_uploadsDirPath;
    }

    /**
     * @return array
     *
     * @throws UploadSettingsException
     */
    final public function getExtensions(): array
    {
        if (empty($this->_extensions)) {
            throw new UploadSettingsException(
                UploadSettingsException::MESSAGE_SETTINGS_EXTENSIONS_IS_EMPTY,
                UploadException::CODE_SETTINGS_EXTENSIONS_IS_EMPTY
            );
        }

        return $this->_extensions;
    }

    /**
     * @return int
     */
    final public function getMaxSize(): int
    {
        if (empty($this->_maxSize)) {
            return UploadSettings::DEFAULT_FILE_SIZE;
        }

        return $this->_maxSize;
    }

    /**
     * @param string $uploadsDir
     */
    private function _setUploadDirPath(string $uploadsDir): void
    {
        $protectedDirPath = UploadSettings::PROTECTED_DIR_PATH;

        if (defined('APP_PROTECTED_DIR_PATH')) {
            $protectedDirPath = APP_PROTECTED_DIR_PATH;
        }

        $this->_uploadsDirPath = sprintf(
            '%s/%s/%s',
            $protectedDirPath,
            $uploadsDir,
            date('Y/m/d/H/i/s')
        );
    }
}
