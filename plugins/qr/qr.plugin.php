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

    const DEFAULT_CELL_SIZE = 5;

    const DEFAULT_CELL_INDENT = 2;

    const DEFAULT_FILE_PATH = '/';

    const DEFAULT_FILE_NAME = 'qr.png';

    public function create(
        ?string $text                 = NULL,
        ?string $filePath             = NULL,
        ?string $fileName             = NULL,
        ?int    $cellSize             = NULL,
        ?int    $cellIndent           = NULL,
        ?string $errorCorrectionLevel = NULL
    ) : ?string
    {
        if (empty($text)) {
            throw new Exception('Text Value Is Empty');
        }

        $filePath   = $this->_getFilePath($filePath);
        $fileName   = $this->_getFileName($fileName);
        $cellSize   = $this->_getCellSize($cellSize);
        $cellIndent = $this->_getCellIndent($cellIndent);

        $errorCorrectionLevel = $this->_getErrorCorrectionLevel(
            $errorCorrectionLevel
        );

        QRcode::png(
            $text,
            $filePath.$fileName,
            $errorCorrectionLevel,
            $cellSize,
            $cellIndent
        );

        if (
            !file_exists($filePath.$fileName) ||
            !is_file($filePath.$fileName)
        ) {
            return NULL;
        }

        return $filePath.$fileName;
    }

    private function _getFilePath(?string $filePath = NULL) : string
    {
        if (empty($filePath)) {
            $filePath = static::DEFAULT_FILE_PATH;
        }

        $filePath = realpath(__DIR__.'/../../../../public/').$filePath;
        $filePath = realpath($filePath);

        if (empty($filePath)) {
            throw new Exception('Invalid File Path Value');
        }

        $filePath = $filePath.'/';

        return $filePath;
    }

    private function _getFileName(?string $fileName = NULL) : string
    {
        $fileName = (string) mb_convert_case($fileName, MB_CASE_LOWER);
        $fileName = preg_replace('/\s/su', '_', $fileName);
        $fileName = preg_replace('/^(.*?)\.png$/su', '$1', $fileName);
        $fileName = preg_replace('/([^a-z0-9_]+)/su', '_', $fileName);
        $fileName = preg_replace('/([_]+)/su', '_', $fileName);
        $fileName = preg_replace('/((^_)|(_$))/su', '', $fileName);

        if (empty($fileName)) {
            $fileName = static::DEFAULT_FILE_NAME;
        }

        if (!preg_match('/^(.*?)\.png$/su', $fileName)) {
            $fileName = $fileName.'.png';
        }

        return $fileName;
    }

    private function _getCellSize(?int $cellSize = NULL) : int
    {
        if (empty($cellSize)) {
            $cellSize = static::DEFAULT_CELL_SIZE;
        }

        return $cellSize;
    }

    private function _getCellIndent(?int $cellIndent = NULL) : int
    {
        if (empty($cellIndent)) {
            $cellIndent = static::DEFAULT_CELL_INDENT;
        }

        return $cellIndent;
    }

    private function _getErrorCorrectionLevel(
        ?string $errorCorrectionLevel = NULL
    ) : string
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
        string $errorCorrectionLevel = ''
    ) : bool
    {
        return $errorCorrectionLevel == static::ERROR_CORRECTION_LEVEL_LOW ||
               $errorCorrectionLevel == static::ERROR_CORRECTION_LEVEL_MIDDLE ||
               $errorCorrectionLevel == static::ERROR_CORRECTION_LEVEL_HIGHT ||
               $errorCorrectionLevel == static::ERROR_CORRECTION_LEVEL_BEST;
    }
}
?>