<?php
/**
 * Get Translation String From Dictionary
 *
 * @param string $word Input String Value
 *
 * @return string Translated String Value
 */
function _t(string $word = '') : string
{
    $translator = new TranslatorPlugin();
    $translator->setUserLanguage();

    return $translator->translate($word);
}
?>