<?php
/**
 * Pugin For Translit String Values
 */
class TranslitPlugin
{
    /**
     * @var string Max Length Of Slug
     */
    const SLUG_MAX_LENGTH = 60;

    /**
     * @var array List Of Cyrillic Characters
     */
    const CYRILLIC_ALPHABET = [
        'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И',
        'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т',
        'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ы', 'Э', 'Ю',
        'Я', 'І', 'Ї', 'Є', 'Ґ', 'а', 'б', 'в', 'г', 'д',
        'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н',
        'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч',
        'ш', 'щ', 'ы', 'э', 'ю', 'я', 'і', 'ї', 'є', 'ґ'
    ];

    /**
     * @var array List Of Translit Characters
     */
    const LATIN_ALPHABET = [
        'A',  'B',   'V',   'G',   'D',   'E',   'E',   'Zh',  'Z',   'Y',
        'J',  'K',   'L',   'M',   'N',   'O',   'P',   'R',   'S',   'T',
        'U',  'F',   'H',   'C',   'Ch',  'Sh',  'Sch', 'Y',   'E',   'Yu',
        'Ya', 'I',   'Yi',  'Ye',  'G',   'a',   'b',   'v',   'g',   'd',
        'e',  'e',   'zh',  'z',   'y',   'j',   'k',   'l',   'm',   'n',
        'o',  'p',   'r',   's',   't',   'u',   'f',   'h',   'c',   'ch',
        'sh', 'sch', 'y',   'e',   'yu',  'ya',  'i',   'yi',  'ye',  'g'
    ];

    /**
     * Convert Cyrillic String Value To Latin Translit
     *
     * @param string|null $cyrillicString Cyrillic String Value
     *
     * @return string|null Value Of Latin Translit String
     */
    public function cyr2lat(?string $cyrillicString = null): ?string
    {
        if (empty($cyrillicString)) {
            return null;
        }

        return str_replace(
            static::CYRILLIC_ALPHABET,
            static::LATIN_ALPHABET,
            $cyrillicString
        );
    }

    /**
     * Convert Cyrillic String Value To URL Slug
     *
     * @param string|null $cyrillicString Cyrillic String Value
     *
     * @return string|null URL Slug
     */
    public function getSlug(?string $cyrillicString = null): ?string
    {
        if (empty($cyrillicString)) {
            return null;
        }

        $latinString = (string) $this->cyr2lat($cyrillicString);
        $latinString = mb_convert_case($latinString, MB_CASE_LOWER);

        $slug = preg_replace('/([^a-z0-9-]+)/su', '-', $latinString);
        $slug = preg_replace('/([\-]+)/su', '-', $slug);
        $slug = preg_replace('/(^-)|(-$)/su', '', $slug);

        $slug = substr($slug, 0, static::SLUG_MAX_LENGTH);
        $slug = preg_replace('/(-$)/su', '', $slug);

        return $slug;
    }
}
