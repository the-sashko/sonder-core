<?php

use Sonder\Plugins\Language\Exceptions\LanguageException;
use Sonder\Plugins\LanguagePlugin;

/**
 * @param string|null $string
 *
 * @return string
 *
 * @throws LanguageException
 */
function __t(?string $string = null): string
{
    return (string)(new LanguagePlugin)->translate($string);
}
