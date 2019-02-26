<?php
    class TranslitLib
    {
        const CYRILLIC_ALPHABET = array(
            'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И',
            'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т',
            'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ы', 'Э', 'Ю',
            'Я', 'І', 'Ї', 'Є', 'Ґ', 'а', 'б', 'в', 'г', 'д',
            'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н',
            'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч',
            'ш', 'щ', 'ы', 'э', 'ю', 'я', 'і', 'ї', 'є', 'ґ'
        );

        const LATIN_ALPHABET = array(
            'A',  'B',   'V',   'G',   'D',   'E',   'E',   'Zh',  'Z',   'Y',
            'J',  'K',   'L',   'M',   'N',   'O',   'P',   'R',   'S',   'T',
            'U',  'F',   'H',   'C',   'Ch',  'Sh',  'Sch', 'Y',   'E',   'Yu',
            'Ya', 'I',   'Yi',  'Ye',  'G',   'a',   'b',   'v',   'g',   'd',
            'e',  'e',   'zh',  'z',   'y',   'j',   'k',   'l',   'm',   'n',
            'o',  'p',   'r',   's',   't',   'u',   'f',   'h',   'c',   'ch',
            'sh', 'sch', 'y',   'e',   'yu',  'ya',  'i',   'yi',  'ye',  'g'
        );

        public function cyr2lat(string $cyrillicString = '') : string
        {
            return str_replace(
                static::CYRILLIC_ALPHABET,
                static::LATIN_ALPHABET,
                $cyrillicString
            );
        }

        public function getSlug(string $inputString = '') : string
        {
            $inputString = $this->cyr2lat($inputString);
            $inputString = mb_convert_case($inputString, MB_CASE_LOWER);
            $inputString = preg_replace(
                '/([^a-z0-9-]+)/su',
                '-',
                $inputString
            );
            $inputString = preg_replace('/([\-]+)/su', '-', $inputString);
            $inputString = preg_replace('/(^-)|(-$)/su','',$inputString);

            return $inputString;
        }
    }
?>