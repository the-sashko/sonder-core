<?php

/**
 * Plugin For Generating HTML Breadcrumbs
 */
class BreadcrumbsPlugin
{
    const MAIN_TEMPLATE_PATH = __DIR__ . '/html/main.html';
    const ELEMENT_TEMPLATE_PATH = __DIR__ . '/html/element.html';
    const LAST_ELEMENT_TEMPLATE_PATH = __DIR__ . '/html/last_element.html';

    /**
     * Get HTML Of Breadcrumbs By Path Current Page In Site Structure
     *
     * @param array|null $pagePath Path Current Page In Site Structure
     *
     * @return string Output HTML Text
     *
     * @throws LanguageException
     */
    public function getHtml(?array $pagePath = null): string
    {
        $mainHtml = $this->_getMainTemplate();

        $lastElementHtml = $this->_getLastElementTemplate();

        if (empty($pagePath)) {
            $lastElementHtml = sprintf(
                $lastElementHtml,
                $this->_getTranslation('Main Page')
            );

            return sprintf($mainHtml, $lastElementHtml);
        }

        $elementHtml = $this->_getElementTemplate();

        $pagePath = array_merge(
            [
                '/' => $this->_getTranslation('Main Page')
            ],
            $pagePath
        );

        $elements = [];

        $lastElementHtml = sprintf($lastElementHtml, array_pop($pagePath));

        foreach ($pagePath as $path => $element) {
            $elements[] = sprintf($elementHtml, $path, $element);
        }

        $elements[] = $lastElementHtml;

        return sprintf($mainHtml, implode($elements));
    }

    /**
     * Get Main Template
     *
     * @return string Main HTML Template
     */
    private function _getMainTemplate(): string
    {
        return file_get_contents(static::MAIN_TEMPLATE_PATH);
    }

    /**
     * Get Element Template
     *
     * @return string Element HTML Template
     */
    private function _getElementTemplate(): string
    {
        return file_get_contents(static::ELEMENT_TEMPLATE_PATH);
    }

    /**
     * Get Last Element Template
     *
     * @return string Last Element HTML Template
     */
    private function _getLastElementTemplate(): string
    {
        return file_get_contents(static::LAST_ELEMENT_TEMPLATE_PATH);
    }

    /**
     * Get Translation Of String If Translation Plugin Included
     *
     * @param string $inputString Input String
     *
     * @return string Translated String
     *
     * @throws LanguageException
     */
    public function _getTranslation(string $inputString): string
    {
        if (function_exists('__t')) {
            return __t($inputString);
        }

        return $inputString;
    }
}
