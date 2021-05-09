<?php
/**
 * Plugin For Static Pages
 */
class PagePlugin
{
    /**
     * @var string Default Teplate Name
     */
    const DEFAULT_TEMPLATE_NAME = 'default';

    /**
     * @var string Default Teplate Page Location
     */
    const DEFAULT_TEMPLATE_PAGE = 'page';

    /**
     * @var string Default Not Found URL
     */
    const DEFAULT_NOT_FOUND_URL = '/error/404/';

    /**
     * @var string Directory With Template Files
     */
    const TEMPLATES_DIR = __DIR__.'/../../../res/tpl';

    /**
     * @var string Directory With Static Pages Files
     */
    const STATIC_PAGES_DIR = __DIR__.'/../../../res/pages';

    /**
     * @var int Count Of Sections In Static Page File
     */
    const STATIC_PAGE_DATA_SECTIONS = 2;

    /**
     * Get Static Page Values Object
     *
     * @param string|null $staticPageName Static Page File Name
     * @param string|null $templateName   Site Template Name
     * @param string|null $templatePage   Site Template Page Name
     *
     * @return StaticPageValuesObject Values Object Of Static Page
     */
    public function getVO(
        ?string $staticPageName = null,
        ?string $templateName  = null,
        ?string $templatePage  = null
    ): StaticPageValuesObject
    {
        $staticPagePath = $this->_getStaticPagePath($staticPageName);

        if (!$this->_isTemplatePageExists($templateName, $templatePage)) {
            throw new PageException(
                PageException::MESSAGE_PLUGIN_TEMPLATE_IS_NOT_EXIST,
                PageException::CODE_PLUGIN_TEMPLATE_IS_NOT_EXIST
            );
        }

        $staticPageData = $this->_getStaticPageData($staticPagePath);

        return new StaticPageValuesObject($staticPageData);
    }

    /**
     * Get Path To Static Page File
     *
     * @param string|null $staticPageName Static Page File Name
     *
     * @return string Path To Static Page File
     */
    private function _getStaticPagePath(?string $staticPageName = null): string
    {
        if (empty($staticPageName)) {
            throw new PageException(
                PageException::MESSAGE_PLUGIN_STATIC_PAGE_NAME_IS_NOT_SET,
                PageException::CODE_PLUGIN_STATIC_PAGE_NAME_IS_NOT_SET
            );
        }

        $notFoundUrl = static::DEFAULT_NOT_FOUND_URL;

        if (
            defined('APP_NOT_FOUND_URL') &&
            !empty(APP_NOT_FOUND_URL) &&
            $_SERVER['REQUEST_URI'] != APP_NOT_FOUND_URL
        ) {
            $notFoundUrl = APP_NOT_FOUND_URL;
        }

        $staticPagePath = sprintf(
            '%s/%s.md',
            static::STATIC_PAGES_DIR,
            $staticPageName
        );

        if (!file_exists($staticPagePath) || !is_file($staticPagePath)) {
            header(sprintf('Location: %s', $notFoundUrl));
            exit(0);
        }

        return $staticPagePath;
    }

    /**
     * Check Is Template Page Exists
     *
     * @param string|null $templateName Site Template Name
     * @param string|null $templatePage Site Template Page Name
     *
     * @return bool Is Template Page Exists
     */
    public function _isTemplatePageExists(
        ?string $templateName = null,
        ?string $templatePage = null
    ): bool
    {
        if (empty($templateName)) {
            $templateName = static::DEFAULT_TEMPLATE_NAME;
        }

        if (empty($templatePage)) {
            $templatePage = static::DEFAULT_TEMPLATE_PAGE;
        }

        $templatePagePath = sprintf(
            '%s/%s/pages/%s.phtml',
            static::TEMPLATES_DIR,
            $templateName,
            $templatePage
        );

        return file_exists($templatePagePath) && is_file($templatePagePath);
    }

    /**
     * Get Static Page Data
     *
     * @param string $staticPagePath Path To Static Page File
     *
     * @return array Data From Static Page File
     */
    private function _getStaticPageData(string $staticPagePath): array
    {
        $markupPlugin = new MarkupPlugin();

        $staticPageData = (string) file_get_contents($staticPagePath);
        $staticPageData = explode("\n\n===\n\n", $staticPageData);

        if (
            empty($staticPageData) ||
            count($staticPageData) != static::STATIC_PAGE_DATA_SECTIONS
        ) {
            throw new PageException(
                PageException::MESSAGE_PLUGIN_STATIC_PAGE_FILE_HAS_BAD_FORMAT,
                PageException::CODE_PLUGIN_STATIC_PAGE_FILE_HAS_BAD_FORMAT
            );
        }

        $staticPageData = [
            'title'   => $staticPageData[0],
            'content' => $staticPageData[1]
        ];

        $staticPageData['title'] = (new SecurityPlugin)->escapeInput(
            $staticPageData['title']
        );

        $staticPageData['content'] = $markupPlugin->normalizeText(
            $staticPageData['content']
        );

        $staticPageData['content'] = $markupPlugin->normalizeSyntax(
            $staticPageData['content']
        );

        $staticPageData['content'] = $markupPlugin->markup2HTML(
            $staticPageData['content']
        );

        return $staticPageData;
    }
}
