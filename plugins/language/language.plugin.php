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
     * @var string Directory With Dictionary Files
     */
    const DICTIONARY_DIR = __DIR__.'/../../../res/lang';

    /**
     * @var string Current Language Of User
     */
    private $_language = null;

    public function __construct()
    {
        $this->_setLanguage();
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

        $dictionaryFilePath = $this->_getDictionaryFilePath();

        if (empty($dictionaryFilePath)) {
            return $string;
        }

        $dataJSON = file_get_contents($dictFile);
        $dictionaryData = (array) json_decode($dataJSON, true);

        if (array_key_exists($string, $dictionaryData)) {
            return $dictionaryData[$string];
        }

        return $string;
    }

    /**
     * Get Path To Dictionary File
     */
    private function _getDictionaryFilePath(): ?string
    {
        if ($this->_language == $this->_getDefaultLanguage()) {
            return null;
        }

        $dictionaryFilePath = sprintf(
            '%s/%s.json',
            static::DICTIONARY_DIR,
            $this->_language
        );

        if (
            !file_exists($dictionaryFilePath) ||
            !is_file($dictionaryFilePath)
        ) {
            $errorMessage = sprintf(
                'Language With Code "%s" Is Not Supported',
                $this->_language
            );
            
            throw new LanguageException(
                $errorMessage,
                LanguageException::LANGUAGE_IS_NOT_SUPPORTED
            );
        }

        return $dictionaryFilePath;
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
}
