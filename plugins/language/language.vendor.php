<?php
class LanguageVendor
{
    public function __construct()
    {
        require_once __DIR__.'/vendor/php-mo.php';
    }

    public function convertPo2Mo(
        ?string $poFilePath = null,
        ?string $moFilePath = null
    ): void
    {
        if (empty($poFilePath)) {
            throw new LanguageException(
                'PO File Path Is Not Set',
                LanguageException::PO_FILE_IS_NOT_SET
            );
        }

        if (!file_exists($poFilePath) || !is_file($poFilePath)) {
            throw new LanguageException(
                'PO File Path Is Not Set',
                LanguageException::PO_FILE_IS_NOT_EXISTS
            );
        }

        if (empty($moFilePath)) {
            throw new LanguageException('MO File Path Is Not Set', LanguageException::MO_FILE_IS_NOT_SET
            );
        }

        if (file_exists($moFilePath) && is_file($moFilePath)) {
            unlink($moFilePath);
        }

        phpmo_convert($poFilePath, $moFilePath);

        chmod($moFilePath, 0775);
    }
}
