<?php
function _t(string $word = '') : string
{
    $translator = new TranslatorPlugin();
    $translator->setUserLanguage();

    return $translator->translate($word);
}
?>