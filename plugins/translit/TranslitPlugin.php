<?php

namespace Sonder\Plugins;

final class TranslitPlugin
{
    const SLUG_MAX_LENGTH = 60;

    const CYRILLIC_ALPHABET = [
        'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И',
        'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т',
        'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ы', 'Э', 'Ю',
        'Я', 'І', 'Ї', 'Є', 'Ґ', 'а', 'б', 'в', 'г', 'д',
        'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н',
        'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч',
        'ш', 'щ', 'ы', 'э', 'ю', 'я', 'і', 'ї', 'є', 'ґ'
    ];

    const LATIN_ALPHABET = [
        'A', 'B', 'V', 'G', 'D', 'E', 'E', 'Zh', 'Z', 'Y',
        'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T',
        'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'E', 'Yu',
        'Ya', 'I', 'Yi', 'Ye', 'G', 'a', 'b', 'v', 'g', 'd',
        'e', 'e', 'zh', 'z', 'y', 'j', 'k', 'l', 'm', 'n',
        'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch',
        'sh', 'sch', 'y', 'e', 'yu', 'ya', 'i', 'yi', 'ye', 'g'
    ];

    /**
     * @param string|null $cyrillicString
     *
     * @return string|null
     */
    final public function cyr2lat(?string $cyrillicString = null): ?string
    {
        if (empty($cyrillicString)) {
            return null;
        }

        return str_replace(
            TranslitPlugin::CYRILLIC_ALPHABET,
            TranslitPlugin::LATIN_ALPHABET,
            $cyrillicString
        );
    }

    /**
     * @param string|null $cyrillicString
     *
     * @return string|null
     */
    final public function getSlug(?string $cyrillicString = null): ?string
    {
        if (empty($cyrillicString)) {
            return null;
        }

        $cyrillicString = preg_replace(
            '/&([a-z]+);/su',
            '',
            $cyrillicString
        );

        $cyrillicString = htmlspecialchars_decode($cyrillicString);

        $cyrillicString = preg_replace(
            '/&([a-z]+);/su',
            '',
            $cyrillicString
        );

        $latinString = (string)$this->cyr2lat($cyrillicString);
        $latinString = mb_convert_case($latinString, MB_CASE_LOWER);

        $slug = preg_replace(
            '/([^a-z0-9-]+)/su',
            '-',
            $latinString
        );

        $slug = preg_replace('/([\-]+)/su', '-', $slug);
        $slug = preg_replace('/(^-)|(-$)/su', '', $slug);

        $slug = substr($slug, 0, TranslitPlugin::SLUG_MAX_LENGTH);

        return preg_replace('/(-$)/su', '', $slug);
    }
}
