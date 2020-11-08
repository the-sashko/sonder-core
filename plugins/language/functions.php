<?php
/**
 * Translate String By Dictionary
 *
 * @param string|null $string Input String Value
 *
 * @return string Translated String Value
 */
function __t(?string $string = null): string
{
    return (string) (new LanguagePlugin)->translate($string);
}
