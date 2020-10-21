<?php
/**
 * Translate String By Dictionary
 *
 * @param string|null $word Input String Value
 *
 * @return string|null Translated String Value
 */
function _t(?string $string = null): string
{
    return (string) (new LanguagePlugin)->translate($string);
}
