<?php

namespace Sonder\Plugins;

use Sonder\Plugins\Language\LanguageVendor;
use Sonder\Plugins\Language\LanguageException;

final class LanguagePlugin
{
    private const DEFAULT_LANGUAGE = 'en';

    private const DEFAULT_LOCALE = 'en_US';

    private const DICTIONARIES_DIR = __DIR__ . '/../../../res/lang/src';

    private const LOCALE_DIR = __DIR__ . '/../../../res/lang/locale';

    private const CONFIG_DIR = __DIR__ . '/../../../config';

    private ?string $_language = null;

    private ?LanguageVendor $_vendor;

    /**
     * @throws LanguageException
     * @throws Session\Exceptions\SessionException
     */
    final public function __construct()
    {
        if (!defined('DEFAULT_LANGUAGE')) {
            define('DEFAULT_LANGUAGE', LanguagePlugin::DEFAULT_LANGUAGE);
        }

        $this->_setLanguage();
        $this->_vendor = new LanguageVendor();
    }

    /**
     * @param string|null $inputString
     * @return string|null
     * @throws LanguageException
     */
    final public function translate(?string $inputString = null): ?string
    {
        if (empty($inputString)) {
            return null;
        }

        if ($this->_language == DEFAULT_LANGUAGE) {
            return $inputString;
        }

        if (!$this->_isDictionaryExists()) {
            $errorMessage = sprintf(
                'Language With Code "%s" Is Not Supported',
                $this->_language
            );

            throw new LanguageException(
                $errorMessage,
                LanguageException::LANGUAGE_IS_NOT_SUPPORTED
            );
        }

        $locale = $this->_getLocale();

        putenv(sprintf('LC_ALL=%s.UTF-8', $locale));
        setlocale(LC_ALL, sprintf('%s.UTF-8', $locale));

        bindtextdomain($this->_language, LanguagePlugin::LOCALE_DIR);
        textdomain($this->_language);

        return gettext($inputString);
    }

    /**
     * @throws LanguageException
     */
    final public function generateDictionaries(): void
    {
        $sourceFilesPattern = sprintf(
            '%s/*.json',
            LanguagePlugin::DICTIONARIES_DIR
        );

        foreach (glob($sourceFilesPattern) as $sourceFilePath) {
            $language = preg_replace(
                '/^(.*?)\/src\/(.*?)\.json$/su',
                '$2',
                $sourceFilePath
            );

            $locale = $this->_getLocale($language);

            $localePath = sprintf('%s/%s', LanguagePlugin::LOCALE_DIR, $locale);

            if (!file_exists($localePath) || !is_dir($localePath)) {
                mkdir($localePath, 0775, true);
            }

            $localePath = sprintf('%s/LC_MESSAGES', $localePath);

            if (!file_exists($localePath) || !is_dir($localePath)) {
                mkdir($localePath, 0775, true);
            }

            $poFilePath = sprintf('%s/%s.po', $localePath, $language);
            $moFilePath = sprintf('%s/%s.mo', $localePath, $language);

            $this->_generateDictionaryFile(
                $sourceFilePath,
                $poFilePath,
                $moFilePath,
                $locale
            );
        }
    }

    /**
     * @param string|null $language
     * @return string
     */
    private function _getLocale(?string $language = null): string
    {
        $defaultLocale = $this->_getDefaultLocale();

        if (empty($language)) {
            $language = $this->_language;
        }

        $localeConfigPath = sprintf(
            '%s/locale.json',
            LanguagePlugin::CONFIG_DIR
        );

        if (!file_exists($localeConfigPath) || !is_file($localeConfigPath)) {
            return $defaultLocale;
        }

        $localeConfig = file_get_contents($localeConfigPath);
        $localeConfig = (array)json_decode($localeConfig, true);

        if (empty($localeConfig)) {
            return $defaultLocale;
        }

        if (
            array_key_exists($language, $localeConfig) &&
            !empty($localeConfig[$language]) &&
            is_scalar($localeConfig[$language])
        ) {
            return (string)$localeConfig[$language];
        }

        return $defaultLocale;
    }

    /**
     * @return bool
     */
    private function _isDictionaryExists(): bool
    {
        $dictionaryFilePath = sprintf(
            '%s/%s.json',
            LanguagePlugin::DICTIONARIES_DIR,
            $this->_language
        );

        if (file_exists($dictionaryFilePath) || is_file($dictionaryFilePath)) {
            return true;
        }

        return false;
    }

    /**
     * @throws LanguageException
     * @throws Session\Exceptions\SessionException
     */
    private function _setLanguage(): void
    {
        $session = new SessionPlugin();

        $language = DEFAULT_LANGUAGE;

        if ($session->has('language')) {
            $language = $session->get('language');
        }

        if (!preg_match('/^([a-z]{2})$/su', $language)) {
            throw new LanguageException(
                sprintf('Language Code "%s" Has Invalid Format', $language),
                LanguageException::INVALID_LANGUAGE_FORMAT
            );
        }

        $this->_language = $language;
    }

    /**
     * @return string
     */
    private function _getDefaultLocale(): string
    {
        $defaultLocale = LanguagePlugin::DEFAULT_LOCALE;

        $mainConfigPath = sprintf('%s/main.json', LanguagePlugin::CONFIG_DIR);

        if (!file_exists($mainConfigPath) || !is_file($mainConfigPath)) {
            return $defaultLocale;
        }

        $mainConfig = file_get_contents($mainConfigPath);
        $mainConfig = (array)json_decode($mainConfig, true);

        if (empty($mainConfig)) {
            return $defaultLocale;
        }

        if (
            array_key_exists('site_locale', $mainConfig) &&
            !empty($mainConfig['site_locale']) &&
            is_scalar($mainConfig['site_locale'])
        ) {
            return (string)$mainConfig['site_locale'];
        }

        return $defaultLocale;
    }

    /**
     * @param string|null $sourceFilePath
     * @param string|null $poFilePath
     * @param string|null $moFilePath
     * @param string|null $locale
     * @throws LanguageException
     */
    private function _generateDictionaryFile(
        ?string $sourceFilePath = null,
        ?string $poFilePath = null,
        ?string $moFilePath = null,
        ?string $locale = null
    ): void {
        if (!$this->_prepareDictionaryFile(
            $sourceFilePath,
            $poFilePath,
            $moFilePath,
            $locale
        )) {
            throw new LanguageException(
                'Can Not Prepare Language Dictionary File',
                LanguageException::CAN_NOT_PREPARE_DICTIONARY_FILE
            );
        }

        $jsonContent = file_get_contents((string)$sourceFilePath);
        $dictionaryRows = (array)json_decode($jsonContent);

        $headerRow = [
            'msgid ""',
            'msgstr ""',
            '"Project-Id-Version: 1.0\n"',
            '"Report-Msgid-Bugs-To: \n"',
            sprintf('"POT-Creation-Date: %s+0000\n"', date('Y-m-d H:m')),
            sprintf('"PO-Revision-Date:%s+0000\n"', date('Y-m-d H:m')),
            sprintf('"Language: %s\n"', (string)$locale),
            '"MIME-Version: 1.0\n"',
            '"Content-Type: text/plain; charset=UTF-8\n"'
        ];

        $headerRow = implode("\n", $headerRow);

        foreach ($dictionaryRows as $key => $value) {
            $row = [
                '#. translation',
                sprintf('msgid "%s"', $key),
                sprintf('msgstr "%s"', $value)
            ];

            $dictionaryRows[$key] = implode("\n", $row);
        }

        $dictionaryRows = implode("\n\n", $dictionaryRows);

        $dictionaryRows = sprintf(
            '%s%s%s',
            $headerRow,
            "\n\n",
            $dictionaryRows
        );

        file_put_contents((string)$poFilePath, $dictionaryRows);

        $this->_vendor->convertPo2Mo($poFilePath, $moFilePath);

        clearstatcache();
        opcache_reset();
    }

    /**
     * @param string|null $sourceFilePath
     * @param string|null $poFilePath
     * @param string|null $moFilePath
     * @param string|null $locale
     * @return bool
     */
    private function _prepareDictionaryFile(
        ?string $sourceFilePath = null,
        ?string $poFilePath = null,
        ?string $moFilePath = null,
        ?string $locale = null
    ): bool {
        if (
            empty($sourceFilePath) ||
            empty($poFilePath) ||
            empty($moFilePath) ||
            empty($locale)
        ) {
            return false;
        }

        if (!file_exists($sourceFilePath) || !is_file($sourceFilePath)) {
            return false;
        }

        if (file_exists($poFilePath) && is_file($poFilePath)) {
            unlink($poFilePath);
        }

        touch($poFilePath);
        chmod($poFilePath, 0775);

        return true;
    }
}
