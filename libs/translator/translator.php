<?php
    trait translator {

        const DEFAULT_LANGUAGE = 'en';

        public $userLanguageCode = null;

        public function translate(string $word = '') : string {
            $langCode = $this->userLanguageCode;

            $dictFile = __DIR__.'/dict/{$langCode}.json';

            if(!file_exists($dictFile)){
                return $word;
            }

            if(!is_file($dictFile)){
                return $word;
            }

            $dataJSON = file_get_contents($dictFile);
            $dictData = json_decode($dataJSON,true);

            if (array_key_exists($word, $dictData)) {
                return $dictData[$word];
            }

            return $word;
        }

        public function setUserLanguage() : void
        {
            $this->userLanguageCode = $this->_getUserLanguageCodeFromSession();
        }

        private function _getUserLanguageCodeFromSession() {
            if (!array_key_exists('user_lang_code', $_SESSION)) {
                return self::DEFAULT_LANGUAGE;
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