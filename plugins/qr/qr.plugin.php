<?php
/**
 * Plugin For Creating QR Codes
 */
class QrPlugin
{
    const ERROR_CORRECTION_LEVEL_LOW = 'L';

    const ERROR_CORRECTION_LEVEL_MIDDLE = 'M';

    const ERROR_CORRECTION_LEVEL_HIGHT = 'Q';

    const ERROR_CORRECTION_LEVEL_BEST = 'H';

    const PUBLIC_DIR_PATH = __DIR__.'/../../../../public';

    const DEFAULT_CELL_SIZE = 5;

    const DEFAULT_CELL_INDENT = 2;

    const DEFAULT_FILE_NAME = 'qr.png';

    public function create(
        ?string $text                 = null,
        ?string $dirPath              = null,
        ?string $fileName             = null,
        ?int    $cellSize             = null,
        ?int    $cellIndent           = null,
        ?string $errorCorrectionLevel = null
    ): ?string
    {
        if (empty($text)) {
            throw new \Exception('Text Value Is Empty');
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

    private function _getDirPath(?string $dirPath = null): ?string
    {
        if (empty($dirPath)) {
            throw new \Exception('Invalid Directory Path Value');
        }

        $dirPath = sprintf('%s/%s', static::PUBLIC_DIR_PATH, $dirPath);

        if (!file_exists($dirPath) || !is_dir($dirPath)) {
            mkdir($dirPath, 0775, true);
        }

        return $dirPath;
    }

    private function _getFileName(?string $fileName = null): string
    {
        $fileName = mb_convert_case((string) $fileName, MB_CASE_LOWER);
        $fileName = preg_replace('/\s/su', '_', $fileName);
        $fileName = preg_replace('/^(.*?)\.png$/su', '$1', $fileName);
        $fileName = preg_replace('/([^a-z0-9_]+)/su', '_', $fileName);
        $fileName = preg_replace('/([_]+)/su', '_', $fileName);
        $fileName = preg_replace('/((^_)|(_$))/su', '', $fileName);

        if (empty($fileName)) {
            $fileName = static::DEFAULT_FILE_NAME;
        }

        if (!preg_match('/^(.*?)\.png$/su', $fileName)) {
            $fileName = sprintf('%s.png', $fileName);
        }

        return $fileName;
    }

    private function _getCellSize(?int $cellSize = null): int
    {
        if (empty($cellSize)) {
            $cellSize = static::DEFAULT_CELL_SIZE;
        }

        return $cellSize;
    }

    private function _getCellIndent(?int $cellIndent = null): int
    {
        if (empty($cellIndent)) {
            $cellIndent = static::DEFAULT_CELL_INDENT;
        }

        return $cellIndent;
    }

    private function _getErrorCorrectionLevel(
        ?string $errorCorrectionLevel = null
    ): string
    {
        if (empty($errorCorrectionLevel)) {
            $errorCorrectionLevel = static::ERROR_CORRECTION_LEVEL_MIDDLE;
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

    private function _isValidErrorCorrectionLevel(
        ?string $errorCorrection = null
    ): bool
    {
        return $errorCorrection == static::ERROR_CORRECTION_LEVEL_LOW ||
               $errorCorrection == static::ERROR_CORRECTION_LEVEL_MIDDLE ||
               $errorCorrection == static::ERROR_CORRECTION_LEVEL_HIGHT ||
               $errorCorrection == static::ERROR_CORRECTION_LEVEL_BEST;
    }
}
