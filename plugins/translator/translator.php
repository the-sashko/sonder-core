<?php
    class TranslatorPlugin {

        const DEFAULT_LANGUAGE = 'en';

        public $userLanguageCode = null;

        public function translate(string $word = '') : string {
            $langCode = $this->userLanguageCode;

            $dictFile = __DIR__."/dict/{$langCode}.json";

            if (!file_exists($dictFile) || !is_file($dictFile)) {
                return $word;
            }

            $dataJSON = file_get_contents($dictFile);
            $dictData = json_decode($dataJSON,true);

            if (array_key_exists($word, $dictData)) {
                return $dictData[$word];
            }

            return $word;
        }

        public function setUserLanguage(string $languageCode = '') : void
        {
            $this->userLanguageCode = $this->_getUserLanguageCodeFromSession();

            if (strlen($languageCode) > 0) {
                $this->userLanguageCode = $languageCode;
            }
        }

        private function _getUserLanguageCodeFromSession() {
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

            if (strlen($langCode) != 2) {
                return self::DEFAULT_LANGUAGE;
            }

            return $langCode;
        }
    }
?>