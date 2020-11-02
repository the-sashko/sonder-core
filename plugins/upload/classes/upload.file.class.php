<?php
namespace Core\Plugins\Upload\Classes;

use Core\Plugins\Upload\Interfaces\IUploadFile;

use Core\Plugins\Upload\Exceptions\UploadFileException;

class UploadFile implements IUploadFile
{
    const DEFAULT_FILE_NAME = 'file';

    const TRANSLIT_DICT = [
        'а' => 'a',   'б' => 'b',   'в' => 'v',   'г' => 'g',
        'ґ' => 'g',   'д' => 'd',   'е' => 'e',   'э' => 'e',
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',   'i' => 'i',
        'ї' => 'i',   'и' => 'i',   'й' => 'y',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n',   'о' => 'o',
        'п' => 'p',   'р' => 'r',   'с' => 's',   'т' => 't',
        'у' => 'u',   'ф' => 'f',   'х' => 'h',   'ц' => 'c',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch', 'ь' => '',
        'ы' => 'y',   'ъ' => '',    'э' => 'e',   'ю' => 'yu',
        'я' => 'ya'
    ];

    private $_name = null;

    private $_extension = null;

    private $_filePath = null;

    private $_size = null;

    private $_error = null;

    public function __construct(
        ?string $fileName = null,
        ?string $filePath = null,
        ?int    $size = null,
        ?int    $error = null
    )
    {
        $fileName = mb_convert_case((string) $fileName, MB_CASE_LOWER);

        if (empty($fileName)) {
            throw new UploadFileException(
                UploadFileException::MESSAGE_FILE_NAME_IS_NOT_SET,
                UploadFileException::CODE_FILE_NAME_IS_NOT_SET
            );
        }

        if (empty($filePath)) {
            throw new UploadFileException(
                UploadFileException::MESSAGE_FILE_PATH_IS_NOT_SET,
                UploadFileException::CODE_FILE_PATH_IS_NOT_SET
            );
        }

        $this->_name      = $this->_normalizeFileName($fileName);
        $this->_extension = $this->_getExtensionFromName($fileName);
        $this->_filePath  = $filePath;
        $this->_size      = (int) $size;
        $this->_error     = (int) $error;
    }

    public function getName(): string
    {
        if (empty($this->_name)) {
            return static::DEFAULT_FILE_NAME;
        }

        return $this->_name;
    }

    public function getExtension(): string
    {
        return (string) $this->_extension;
    }

    public function getFilePath(): string
    {
        if (empty($this->_filePath)) {
            throw new UploadFileException(
                UploadFileException::MESSAGE_FILE_PATH_IS_NOT_SET,
                UploadFileException::CODE_FILE_PATH_IS_NOT_SET
            );
        }

        return $this->_filePath;
    }

    public function getSize(): int
    {
        return (int) $this->_size;
    }

    public function getError(): int
    {
        return (int) $this->_error;
    }

    private function _getExtensionFromName(?string $fileName = null): ?string
    {
        if (empty($fileName)) {
            return null;
        }

        if (!preg_match('/^(.*?)\.([a-z]+)$/su', $fileName)) {
            return null;
        }

        return preg_replace('/^(.*?)\.([a-z]+)$/su', '$2', $fileName);
    }

    private function _normalizeFileName(?string $fileName = null): string
    {
        if (empty($fileName)) {
            return static::DEFAULT_FILE_NAME;
        }

        if (!preg_match('/^(.*?)\.([a-z]+)$/su', $fileName)) {
            return static::DEFAULT_FILE_NAME;
        }

        $fileName = preg_replace('/^(.*?)\.([a-z]+)$/su', '$1', $fileName);
        $fileName = (string) $this->_getTranslit($fileName);
        $fileName = preg_replace('/([^a-z0-9]+)/su', '-', $fileName);
        $fileName = preg_replace('/([\-]+)/su', '-', $fileName);
        $fileName = preg_replace('/(^\-)|(\-$)/su', '', $fileName);

        if (empty($fileName)) {
            $fileName = static::DEFAULT_FILE_NAME;
        }

        return $fileName;
    }

    private function _getTranslit(?string $text = null): ?string
    {
        if (empty($text)) {
            return null;
        }

        return strtr($text, static::TRANSLIT_DICT);
    }
}
