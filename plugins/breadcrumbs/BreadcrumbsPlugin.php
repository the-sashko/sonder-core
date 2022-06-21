<?php

namespace Sonder\Plugins;

use Sonder\Plugins\Language\LanguageException;

use function Sonder\__t;

final class BreadcrumbsPlugin
{
    private const MAIN_TEMPLATE_PATH = __DIR__ . '/html/main.html';
    private const ELEMENT_TEMPLATE_PATH = __DIR__ . '/html/element.html';
    private const LAST_ELEMENT_TEMPLATE_PATH = __DIR__ . '/html/last_element.html';

    /**
     * @param array|null $pagePath
     * @return string
     * @throws LanguageException
     */
    final public function getHtml(?array $pagePath = null): string
    {
        $mainHtml = $this->_getMainTemplate();

        $lastElementHtml = $this->_getLastElementTemplate();

        if (empty($pagePath)) {
            $lastElementHtml = sprintf(
                $lastElementHtml,
                $this->_getTranslation()
            );

            return sprintf($mainHtml, $lastElementHtml);
        }

        $elementHtml = $this->_getElementTemplate();

        $pagePath = array_merge(
            [
                '/' => $this->_getTranslation()
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
     * @return string
     */
    private function _getMainTemplate(): string
    {
        return file_get_contents(BreadcrumbsPlugin::MAIN_TEMPLATE_PATH);
    }

    /**
     * @return string
     */
    private function _getElementTemplate(): string
    {
        return file_get_contents(
            BreadcrumbsPlugin::ELEMENT_TEMPLATE_PATH
        );
    }

    /**
     * @return string
     */
    private function _getLastElementTemplate(): string
    {
        return file_get_contents(
            BreadcrumbsPlugin::LAST_ELEMENT_TEMPLATE_PATH
        );
    }

    /**
     * @return string
     * @throws LanguageException
     */
    private function _getTranslation(): string
    {
        if (function_exists('__t')) {
            return __t('Main Page');
        }

        return 'Main Page';
    }
}
