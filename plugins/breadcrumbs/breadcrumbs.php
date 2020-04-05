<?php
/**
 * Plugin For Generating HTML Breadcrumbs
 */
class BreadcrumbsPlugin
{
    const MAIN_TEMPLATE_PATH         = __DIR__.'/html/main.html';
    const ELEMENT_TEMPLATE_PATH      = __DIR__.'/html/element.html';
    const LAST_ELEMENT_TEMPLATE_PATH = __DIR__.'/html/last_element.html';

    /**
     * Get HTML Of Breadcrumbs By Path Current Page In Site Structure
     *
     * @param array $pagePath Path Current Page In Site Structure
     *
     * @return string Output HTML Text
     */
    public function getHTML(?array $pagePath = null): string
    {
        $mainHtml = $this->_getMainTemplate();

        $lastElementHtml = $this->_getLastElementTemplate();

        if (empty($pagePath)) {
            $lastElementHtml = sprintf($lastElementHtml, _t('Main Page'));

            return sprintf($mainHtml, $lastElementHtml);
        }

        $elementHtml = $this->_getElementTemplate();

        $pagePath = array_merge(['/' => _t('Main Page')], $pagePath);

        $elements = [];

        $lastElementHtml = sprintf($lastElementHtml, array_pop($pagePath));

        foreach ($pagePath as $path => $element) {
            $elements[] = sprintf($elementHtml, $path, $element);
        }

        $elements[] = $lastElementHtml;

        return sprintf($mainHtml, implode($elements));
    }

    /**
     * Get Main Temlate
     *
     * @return string Main HTML Template
     */
    private function _getMainTemplate(): string
    {
        return file_get_contents(static::MAIN_TEMPLATE_PATH);
    }

    /**
     * Get Element Temlate
     *
     * @return string Element HTML Template
     */
    private function _getElementTemplate(): string
    {
        return file_get_contents(static::ELEMENT_TEMPLATE_PATH);
    }

    /**
     * Get Last Element Temlate
     *
     * @return string Last Element HTML Template
     */
    private function _getLastElementTemplate(): string
    {
        return file_get_contents(static::LAST_ELEMENT_TEMPLATE_PATH);
    }
}
