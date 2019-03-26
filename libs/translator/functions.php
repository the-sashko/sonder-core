<?php
function _t(string $word = '') : string
{
    $translator = new translatorLib();
    $translator->setUserLanguage();

    return $translator->translate($word);
}
?>