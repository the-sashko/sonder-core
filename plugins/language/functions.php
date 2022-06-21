<?php

namespace Sonder;

use Sonder\Plugins\Language\LanguageException;
use Sonder\Plugins\LanguagePlugin;

/**
 * @param string|null $string
 * @return string
 * @throws LanguageException
 */
function __t(?string $string = null): string
{
    return (string)(new LanguagePlugin)->translate($string);
}
