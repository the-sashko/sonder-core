<?php
/**
 * Plugin For Traslation String Values
 */
class TranslatorPlugin
{
    /**
     * @var string Default Language Code
     */
    const DEFAULT_LANGUAGE = 'en';

    /**
     * @var string User Language Code
     */
    public $userLanguageCode = NULL;

    /**
     * Get Translation String From Dictionary
     *
     * @param string $word Input String Value
     *
     * @return string Translated String Value
     */
    public function translate(string $word = '') : string
    {
        $langCode = $this->userLanguageCode;

        $dictFile = __DIR__."/dict/{$langCode}.json";

        if (!file_exists($dictFile) || !is_file($dictFile)) {
            return $word;
        }

        $dataJSON = file_get_contents($dictFile);
        $dictData = (array) json_decode($dataJSON, true);

        if (array_key_exists($word, $dictData)) {
            return $dictData[$word];
        }

        return $word;
    }

    /**
     * Set User Language Code
     *
     * @param string $languageCode Language Code
     */
    public function setUserLanguage(string $languageCode = '') : void
    {
        $this->userLanguageCode = $this->_getUserLanguageCodeFromSession();

        if (strlen($languageCode) > 0) {
            $this->userLanguageCode = $languageCode;
        }
    }

    /**
     * Get User Language Code
     *
     * @return string Language Code
     */
    private function _getUserLanguageCodeFromSession() : string
    {
        if (!array_key_exists('user_lang_code', $_SESSION)) {
            if (defined('DEFAULT_LANGUAGE')) {
                return DEFAULT_LANGUAGE;
            } else {
                return self::DEFAULT_LANGUAGE;
            }
        }

        $langCode = $_SESSION['user_lang_code'];

        $langCode = trim($langCode);
        $langCode = (string)mb_convert_case($langCode,MB_CASE_LOWER);

        if (2 !== strlen($langCode)) {
            return self::DEFAULT_LANGUAGE;
        }

        return $langCode;
    }
}
?>