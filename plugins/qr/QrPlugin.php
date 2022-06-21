<?php

namespace Sonder\QrPlugin;

use Exception;
use QRcode;

final class QrPlugin
{
    private const ERROR_CORRECTION_LEVEL_LOW = 'L';

    private const ERROR_CORRECTION_LEVEL_MIDDLE = 'M';

    private const ERROR_CORRECTION_LEVEL_HEIGHT = 'Q';

    private const ERROR_CORRECTION_LEVEL_BEST = 'H';

    private const PUBLIC_DIR_PATH = __DIR__.'/../../../../public';

    private const DEFAULT_CELL_SIZE = 5;

    private const DEFAULT_CELL_INDENT = 2;

    private const DEFAULT_FILE_NAME = 'qr.png';

    /**
     * @param string|null $text
     * @param string|null $dirPath
     * @param string|null $fileName
     * @param int|null $cellSize
     * @param int|null $cellIndent
     * @param string|null $errorCorrectionLevel
     *
     * @return string|null
     *
     * @throws Exception
     */
    final public function create(
        ?string $text                 = null,
        ?string $dirPath              = null,
        ?string $fileName             = null,
        ?int    $cellSize             = null,
        ?int    $cellIndent           = null,
        ?string $errorCorrectionLevel = null
    ): ?string
    {
        if (empty($text)) {
            throw new Exception('Text Value Is Empty');
        }

        $dirPath    = $this->_getDirPath($dirPath);
        $fileName   = $this->_getFileName($fileName);
        $cellSize   = $this->_getCellSize($cellSize);
        $cellIndent = $this->_getCellIndent($cellIndent);

        if (empty($dirPath)) {
            return null;
        }

        $filePath = sprintf('%s/%s', $dirPath, $fileName);

        $errorCorrectionLevel = $this->_getErrorCorrectionLevel(
            $errorCorrectionLevel
        );

        QRcode::png(
            $text,
            $filePath,
            $errorCorrectionLevel,
            $cellSize,
            $cellIndent
        );

        if (!file_exists($filePath) || !is_file($filePath)) {
            return null;
        }

        chmod($filePath, 0775);

        return $filePath;
    }

    /**
     * @param string|null $dirPath
     *
     * @return string|null
     *
     * @throws Exception
     */
    private function _getDirPath(?string $dirPath = null): ?string
    {
        if (empty($dirPath)) {
            throw new Exception('Invalid Directory Path Value');
        }

        $publicDirPath = QrPlugin::PUBLIC_DIR_PATH;

        if (defined('APP_PUBLIC_DIR_PATH')) {
            $publicDirPath = APP_PUBLIC_DIR_PATH;
        }

        $dirPath = sprintf('%s/%s', $publicDirPath, $dirPath);

        if (!file_exists($dirPath) || !is_dir($dirPath)) {
            mkdir($dirPath, 0775, true);
        }

        return $dirPath;
    }

    /**
     * @param string|null $fileName
     *
     * @return string
     */
    private function _getFileName(?string $fileName = null): string
    {
        $fileName = mb_convert_case((string) $fileName, MB_CASE_LOWER);
        $fileName = preg_replace('/\s/su', '_', $fileName);
        $fileName = preg_replace('/^(.*?)\.png$/su', '$1', $fileName);
        $fileName = preg_replace('/([^a-z0-9_]+)/su', '_', $fileName);
        $fileName = preg_replace('/([_]+)/su', '_', $fileName);
        $fileName = preg_replace('/((^_)|(_$))/su', '', $fileName);

        if (empty($fileName)) {
            $fileName = QrPlugin::DEFAULT_FILE_NAME;
        }

        if (!preg_match('/^(.*?)\.png$/su', $fileName)) {
            $fileName = sprintf('%s.png', $fileName);
        }

        return $fileName;
    }

    /**
     * @param int|null $cellSize
     *
     * @return int
     */
    private function _getCellSize(?int $cellSize = null): int
    {
        if (empty($cellSize)) {
            $cellSize = QrPlugin::DEFAULT_CELL_SIZE;
        }

        return $cellSize;
    }

    /**
     * @param int|null $cellIndent
     *
     * @return int
     */
    private function _getCellIndent(?int $cellIndent = null): int
    {
        if (empty($cellIndent)) {
            $cellIndent = QrPlugin::DEFAULT_CELL_INDENT;
        }

        return $cellIndent;
    }

    /**
     * @param string|null $errorCorrectionLevel
     *
     * @return string
     *
     * @throws Exception
     */
    private function _getErrorCorrectionLevel(
        ?string $errorCorrectionLevel = null
    ): string
    {
        if (empty($errorCorrectionLevel)) {
            $errorCorrectionLevel = QrPlugin::ERROR_CORRECTION_LEVEL_MIDDLE;
        }

        $errorCorrectionLevel = (string) mb_convert_case(
            $errorCorrectionLevel,
            MB_CASE_UPPER
        );

        if (!$this->_isValidErrorCorrectionLevel($errorCorrectionLevel)) {
            throw new Exception('Invalid Error Correction Level Value');
        }

        return $errorCorrectionLevel;
    }

    /**
     * @param string|null $errorCorrection
     *
     * @return bool
     */
    private function _isValidErrorCorrectionLevel(
        ?string $errorCorrection = null
    ): bool
    {
        return $errorCorrection == QrPlugin::ERROR_CORRECTION_LEVEL_LOW ||
               $errorCorrection == QrPlugin::ERROR_CORRECTION_LEVEL_MIDDLE ||
               $errorCorrection == QrPlugin::ERROR_CORRECTION_LEVEL_HEIGHT ||
               $errorCorrection == QrPlugin::ERROR_CORRECTION_LEVEL_BEST;
    }
}
