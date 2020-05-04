<?php
/**
 * Plugin For Traslation String Values
 */
class LanguagePlugin
{
    /**
     * @var string Default Language
     */
    const DEFAULT_LANGUAGE = 'en';

    /**
     * @var string Default Locale
     */
    const DEFAULT_LOCALE = 'en_US';

    /**
     * @var string Directory With Dictionary Files
     */
    const DICTIONARIES_DIR = __DIR__.'/../../../res/lang/src';

    /**
     * @var string Directory With Dictionary Files
     */
    const LOCALE_DIR = __DIR__.'/../../../res/lang/locale';

    /**
     * @var string Directory With Dictionary Files
     */
    const CONFIG_DIR = __DIR__.'/../../../config';

    /**
     * @var string Current Language Of User
     */
    private $_language = null;

    /**
     * @var LanguageVendor|null LanguageVendor Class Instance
     */
    private $_vendor = null;

    public function __construct()
    {
        $this->_setLanguage();
        $this->_vendor = new LanguageVendor();
    }

    /**
     * Translate String By Dictionary
     *
     * @param string|null $strings Input String Value
     *
     * @return string|null Translated String Value
     */
    public function translate(?string $string = null): ?string
    {
        if (empty($string)) {
            return null;
        }

        if ($this->_language == $this->_getDefaultLanguage()) {
            return $string;
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

        putenv(sprintf('LC_ALL=%s', $locale));
        setlocale(LC_ALL, $locale);

        bindtextdomain($this->_language, static::LOCALE_DIR);

        textdomain($this->_language);

        return gettext($string);
    }

    /**
     * Generate Dictionary Files By JSON Sources
     */
    public function generateDictionaries(): void
    {
        $sourceFilesPattern = sprintf(
            '%s/*.json',
            static::DICTIONARIES_DIR
        );

        foreach (glob($sourceFilesPattern) as $sourceFilePath) {
            $language = preg_replace(
                '/^(.*?)\/src\/(.*?)\.json$/su',
                '$2',
                $sourceFilePath
            );

            $locale = $this->_getLocale($language);

            $localePath = sprintf('%s/%s', static::LOCALE_DIR, $locale);

            if (!file_exists($localePath) || !is_dir($localePath)) {
                mkdir($localePath);
                chmod($localePath, 0775);
            }


            $localePath = sprintf('%s/LC_MESSAGES', $localePath);

            if (!file_exists($localePath) || !is_dir($localePath)) {
                mkdir($localePath);
                chmod($localePath, 0775);
            }

            $poFilePath = sprintf('%s/%s.po', $localePath, $language);
            $moFilePath = sprintf('%s/%s.mo', $localePath, $language);

            $this->_generateDictionaryFile(
                $sourceFilePath,
                $poFilePath,
                $moFilePath
            );
        }
    }

    private function _getLocale(?string $language = null): string
    {
        $defaultLocale = $this->_getDefaultLocale();

        if (empty($language)) {
            $language = $this->_language;
        }

        $localeConfigPath = sprintf('%s/locale.json', static::CONFIG_DIR);

        if (!file_exists($localeConfigPath) || !is_file($localeConfigPath)) {
            return $defaultLocale;
        }

        $localeConfig = file_get_contents($localeConfigPath);
        $localeConfig = (array) json_decode($localeConfig, true);

        if (empty($localeConfig)) {
            return $defaultLocale;
        }

        if (
            array_key_exists($language, $localeConfig) &&
            !empty($localeConfig[$language]) &&
            is_scalar($localeConfig[$language])
        ) {
            return (string) $localeConfig[$language];
        }

        return $defaultLocale;
    }

    /**
     * Check Is Exists Dictionary For Current Language
     *
     * @return bool Is Exists Dictionary For Current Language
     */
    private function _isDictionaryExists(): bool
    {
        $dictionaryFilePath = sprintf(
            '%s/%s.json',
            static::DICTIONARIES_DIR,
            $this->_language
        );

        if (file_exists($dictionaryFilePath) || is_file($dictionaryFilePath)) {
            return true;
        }

        return false;
    }

    /**
     * Set Current Language Of User
     */
    private function _setLanguage(): void
    {
        $session = new SessionPlugin();

        $language = $this->_getDefaultLanguage();

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
     * Get Default Language
     */
    private function _getDefaultLanguage(): string
    {
        if (defined('DEFAULT_LANGUAGE')) {
            return DEFAULT_LANGUAGE;
        }

        return static::DEFAULT_LANGUAGE;
    }


    private function _getDefaultLocale(): string
    {
        $defaultLocale = static::DEFAULT_LOCALE;

        $mainConfigPath = sprintf('%s/main.json', static::CONFIG_DIR);

        if (!file_exists($mainConfigPath) || !is_file($mainConfigPath)) {
            return $defaultLocale;
        }

        $mainConfig = file_get_contents($mainConfigPath);
        $mainConfig = (array) json_decode($mainConfig, true);

        if (empty($mainConfig)) {
            return $defaultLocale;
        }

        if (
            array_key_exists('site_locale', $mainConfig) &&
            !empty($mainConfig['site_locale']) &&
            is_scalar($mainConfig['site_locale'])
        ) {
            return (string) $mainConfig['site_locale'];
        }

        return $defaultLocale;
    }

    /**
     * Generate Dictionary File By JSON Source
     *
     * @return bool Is Dictionary File Successfully Generated
     */
    private function _generateDictionaryFile(
        ?string $sourceFilePath = null,
        ?string $poFilePath     = null,
        ?string $moFilePath     = null
    ): bool
    {
        if (
            empty($sourceFilePath) ||
            empty($poFilePath) ||
            empty($moFilePath)
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

        $jsonContent    = file_get_contents($sourceFilePath);
        $dictionaryRows = (array) json_decode($jsonContent);

        foreach ($dictionaryRows as $key => $value) {
            $row = sprintf('msgid "%s"', $key);
            $row = sprintf('%s%smsgstr "%s"', $row, "\n", $value);

            $dictionaryRows[$key] = $row;
        }

        file_put_contents($poFilePath, implode("\n", $dictionaryRows));

        $this->_vendor->convertPo2Mo($poFilePath, $moFilePath);

        return true;
    }
}