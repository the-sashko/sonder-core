<?php

namespace Sonder\Plugins\Upload\Classes;

use Sonder\Plugins\Upload\Exceptions\UploadException;
use Sonder\Plugins\Upload\Exceptions\UploadFileException;
use Sonder\Plugins\Upload\Interfaces\IUploadFile;

final class UploadFile implements IUploadFile
{
    private const DEFAULT_FILE_NAME = 'file';

    private const TRANSLIT_DICT = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g',
        'ґ' => 'g', 'д' => 'd', 'е' => 'e', 'є' => 'e',
        'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'i' => 'i',
        'ї' => 'i', 'и' => 'i', 'й' => 'y', 'к' => 'k',
        'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
        'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
        'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
        'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ь' => '',
        'ы' => 'y', 'ъ' => '', 'э' => 'e', 'ю' => 'yu',
        'я' => 'ya'
    ];

    /**
     * @var string|null
     */
    private ?string $_name = null;

    /**
     * @var string|null
     */
    private ?string $_extension = null;

    /**
     * @var string|null
     */
    private ?string $_filePath = null;

    /**
     * @var int|null
     */
    private ?int $_size = null;

    /**
     * @var int|null
     */
    private ?int $_error = null;

    /**
     * @param string|null $fileName
     * @param string|null $filePath
     * @param int|null $size
     * @param int|null $error
     *
     * @throws UploadFileException
     */
    final public function __construct(
        ?string $fileName = null,
        ?string $filePath = null,
        ?int    $size = null,
        ?int    $error = null
    )
    {
        $fileName = mb_convert_case((string)$fileName, MB_CASE_LOWER);

        if (empty($fileName)) {
            throw new UploadFileException(
                UploadFileException::MESSAGE_FILE_NAME_IS_NOT_SET,
                UploadException::CODE_FILE_NAME_IS_NOT_SET
            );
        }

        if (empty($filePath)) {
            throw new UploadFileException(
                UploadFileException::MESSAGE_FILE_PATH_IS_NOT_SET,
                UploadException::CODE_FILE_PATH_IS_NOT_SET
            );
        }

        $this->_name = $this->_normalizeFileName($fileName);
        $this->_extension = $this->_getExtensionFromName($fileName);
        $this->_filePath = $filePath;
        $this->_size = (int)$size;
        $this->_error = (int)$error;
    }

    /**
     * @return string
     */
    final public function getName(): string
    {
        if (empty($this->_name)) {
            return UploadFile::DEFAULT_FILE_NAME;
        }

        return $this->_name;
    }

    /**
     * @return string
     */
    final public function getExtension(): string
    {
        return (string)$this->_extension;
    }

    /**
     * @return string
     *
     * @throws UploadFileException
     */
    final public function getFilePath(): string
    {
        if (empty($this->_filePath)) {
            throw new UploadFileException(
                UploadFileException::MESSAGE_FILE_PATH_IS_NOT_SET,
                UploadException::CODE_FILE_PATH_IS_NOT_SET
            );
        }

        return $this->_filePath;
    }

    /**
     * @return int
     */
    final public function getSize(): int
    {
        return (int)$this->_size;
    }

    /**
     * @return int
     */
    final public function getError(): int
    {
        return (int)$this->_error;
    }

    /**
     * @param string|null $fileName
     *
     * @return string|null
     */
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

    /**
     * @param string|null $fileName
     *
     * @return string
     */
    private function _normalizeFileName(?string $fileName = null): string
    {
        if (empty($fileName)) {
            return UploadFile::DEFAULT_FILE_NAME;
        }

        if (!preg_match('/^(.*?)\.([a-z]+)$/su', $fileName)) {
            return UploadFile::DEFAULT_FILE_NAME;
        }

        $fileName = preg_replace('/^(.*?)\.([a-z]+)$/su', '$1', $fileName);
        $fileName = (string)$this->_getTranslit($fileName);
        $fileName = preg_replace('/([^a-z0-9]+)/su', '-', $fileName);
        $fileName = preg_replace('/([\-]+)/su', '-', $fileName);
        $fileName = preg_replace('/(^-)|(-$)/su', '', $fileName);

        if (empty($fileName)) {
            $fileName = UploadFile::DEFAULT_FILE_NAME;
        }

        return $fileName;
    }

    /**
     * @param string|null $text
     *
     * @return string|null
     */
    private function _getTranslit(?string $text = null): ?string
    {
        if (empty($text)) {
            return null;
        }

        return strtr($text, UploadFile::TRANSLIT_DICT);
    }
}
