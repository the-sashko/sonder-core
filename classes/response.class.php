<?php

/**
 * Core Response Class
 */
class Response extends CommonCore
{
    /**
     * @var string Separator For Web Page Title Sections
     */
    const PAGE_TITLE_SEPARATOR = ' / ';

    /**
     * @var int Max Length Of SEO Description
     */
    const SEO_DESCRIPTION_MAX_LENGTH = 300;

    /**
     * @var string Default Templater Area
     */
    const DEFAULT_AREA = 'default';

    /**
     * @var string|null Templater Area
     */
    private ?string $_area = null;

    /**
     * @throws CoreException
     */
    public function __construct()
    {
        parent::__construct();

        if (defined('APP_AREA')) {
            $this->_area = APP_AREA;
        }

        if (empty($this->_area)) {
            $this->_area = static::DEFAULT_AREA;
        }
    }

    /**
     * Return Data In JSON Format
     *
     * @param bool $status Is Request Successful
     * @param array|null $data Output Data
     */
    final public function returnJson(
        bool   $status = true,
        ?array $data = null
    ): void
    {
        $jsonData = [
            'status' => $status,
            'data' => $data
        ];

        $jsonData = json_encode($jsonData);

        header('Content-Type: application/json');
        echo $jsonData;

        exit(0);
    }

    /**
     * Display Static Page
     *
     * @param string|null $staticPageName Static Page File Name
     * @param string|null $templatePage Site Template Page Name
     * @param array|null $params Params Data For Templates
     * @param int $ttl Time To Live Of Template Cache
     *
     * @throws CoreException
     * @throws LanguageException
     */
    final public function displayStaticPage(
        ?string $staticPageName = null,
        ?string $templatePage = null,
        ?array  $params = null,
        int     $ttl = 0
    ): void
    {
        $pagePlugin = $this->getPlugin('page');

        if (empty($templatePage)) {
            $templatePage = PagePlugin::DEFAULT_TEMPLATE_PAGE;
        }

        $staticPageVO = $pagePlugin->getVO(
            $staticPageName,
            $this->_area,
            $templatePage
        );

        $staticPageVO->setTitle(__t($staticPageVO->getTitle()));
        $staticPageVO->setContent(__t($staticPageVO->getContent()));

        $pagePath = [
            '#' => $staticPageVO->getTitle()
        ];

        $seoDescription = $staticPageVO->getContent();
        $seoDescription = strip_tags($seoDescription);

        $seoDescription = preg_replace('/\s+/su', ' ', $seoDescription);
        $seoDescription = preg_replace('/(^\s)|(\s$)/su', '', $seoDescription);

        if (strlen($seoDescription) > static::SEO_DESCRIPTION_MAX_LENGTH - 1) {
            $seoDescription = substr(
                $seoDescription,
                0,
                static::SEO_DESCRIPTION_MAX_LENGTH - 1
            );

            $seoDescription = preg_replace(
                '/^(.*?)\s([^\s]+)$/su',
                '$1',
                $seoDescription
            );

            $seoDescription = sprintf('%s…', $seoDescription);
        }

        $params['staticPage'] = $staticPageVO;
        $params['pagePath'] = $pagePath;
        $params['seoTitle'] = $staticPageVO->getTitle();

        if (!array_key_exists('meta', $params)) {
            $params['meta'] = [];
        }

        $params['meta']['description'] = $seoDescription;

        $this->render(
            $templatePage,
            $params,
            $ttl
        );
    }

    /**
     * Display Error Page
     *
     * @param string $errorMessage HTTP Error Message
     * @param int $errorCode HTTP Error Code
     *
     * @throws CoreException
     */
    final public function displayErrorPage(
        string $errorMessage,
        int    $errorCode
    ): void
    {
        $pagePath = [
            '#' => $errorMessage
        ];

        $this->render(
            'error',
            [
                'errorMessage' => $errorMessage,
                'errorCode' => $errorCode,
                'pagePath' => $pagePath
            ]
        );
    }

    /**
     * Return Data In HTML Format
     *
     * @param string|null $templatePage Template Page Name
     * @param array|null $params Params Data For Templates
     * @param int $ttl Time To Live Of Template Cache
     *
     * @throws CoreException
     */
    final public function render(
        ?string $templatePage = null,
        ?array  $params = null,
        int     $ttl = 0
    ): void
    {
        $params = (array)$params;

        $params['siteLogo'] = $this->configData['seo']['image'];

        $params['currentHost'] = $this->currentHost;
        $params['currentUrl'] = $this->currentUrl;

        if (!array_key_exists('pagePath', $params)) {
            $params['pagePath'] = [];
        }

        if (!array_key_exists('meta', $params)) {
            $params['meta'] = [];
        }

        $params['meta'] = $this->_getMetaParams(
            $params['pagePath'],
            $params['meta']
        );

        $params['assetsVersion'] = $this->_getAssetsVersion();

        $breadcrumbs = $this->getPlugin('breadcrumbs');
        $breadcrumbs = $breadcrumbs->getHtml($params['pagePath']);

        $params['breadcrumbs'] = $breadcrumbs;
        $params['currentUrl'] = $this->currentUrl;

        $templater = $this->getPlugin('templater');

        $templater->setArea($this->_area);

        $this->execHooks('onBeforeRender', $params);

        $templater->render($templatePage, $params, $ttl);

        $this->execHooks('onAfterRender', $params);

        exit(0);
    }

    /**
     * Get Version Of Assets Files
     *
     * @return string
     */
    private function _getAssetsVersion(): string
    {
        if (!defined('APP_MODE') || APP_MODE == 'dev') {
            return sprintf('%d_develop', time());
        }

        $mainConfig = $this->configData['main'];

        if (
            array_key_exists('assets_version', $mainConfig) &&
            !empty($mainConfig['assets_version'])
        ) {
            return (string)$mainConfig['assets_version'];
        }

        return '0';
    }

    /**
     * Get HTML Meta Tag Values
     *
     * @param array|null $pagePath Current Page Path In Site Structure
     * @param array|null $meta Input HTML Meta Tag Values
     *
     * @return array Output HTML Meta Tag Values
     *
     * @throws Exception
     */
    private function _getMetaParams(
        ?array $pagePath = null,
        ?array $meta = null
    ): array
    {
        $pagePath = (array)$pagePath;
        $meta = (array)$meta;

        $metaParams = $this->configData['seo'];
        $mainConfigData = $this->configData['main'];

        if (!array_key_exists('image', $metaParams)) {
            throw new \Exception('SEO Config Has Bad Format');
        }

        if (
            !array_key_exists('site_name', $mainConfigData) ||
            !array_key_exists('site_slogan', $mainConfigData) ||
            !array_key_exists('site_locale', $mainConfigData) ||
            !array_key_exists('launch_date', $mainConfigData)
        ) {
            throw new \Exception('Main Config Has Bad Format');
        }

        if (array_key_exists('description', $meta)) {
            $metaParams['description'] = $meta['description'];
        }

        $metaParams['title'] = $mainConfigData['site_name'];
        $metaParams['site_name'] = $mainConfigData['site_name'];
        $metaParams['site_slogan'] = $mainConfigData['site_slogan'];
        $metaParams['locale'] = $mainConfigData['site_locale'];

        if (!empty($pagePath)) {
            $metaParams['title'] = sprintf(
                '%s%s%s',
                $this->_getTitleByPagePath($pagePath),
                static::PAGE_TITLE_SEPARATOR,
                $metaParams['title']
            );
        }

        $this->_setMetaParamsImage($metaParams, $meta);
        $this->_setMetaCanonicalUrl($metaParams, $meta);
        $this->_setMetaParamsCopyright($metaParams, $mainConfigData);

        return $metaParams;
    }

    /**
     * Set HTML Copyright Value
     *
     * @param array $metaParams Output HTML Meta Data
     * @param array $mainConfigData Main Config Values
     */
    private function _setMetaParamsCopyright(
        array &$metaParams,
        array $mainConfigData
    ): void
    {
        $launchYear = date('Y', strtotime($mainConfigData['launch_date']));
        $siteName = null;

        if (array_key_exists('site_name', $metaParams)) {
            $siteName = $metaParams['site_name'];
        }

        $copyrightDate = date('Y');

        if (date('Y') !== $launchYear) {
            $copyrightDate = sprintf('%s-%s', $launchYear, date('Y'));
        }

        $metaParams['copyright'] = sprintf(
            '&copy; %s %s',
            (string)$siteName,
            (string)$copyrightDate
        );
    }

    /**
     * Set HTML Image Meta Tag Value
     *
     * @param array $metaParams Output HTML Meta Tag Values
     * @param array $meta Input HTML Meta Tag Values
     */
    private function _setMetaParamsImage(array &$metaParams, array $meta): void
    {
        if (array_key_exists('image', $meta)) {
            $metaParams['image'] = $meta['image'];
        }

        if (
            array_key_exists('image', $metaParams) &&
            !empty($metaParams['image'])
        ) {
            $metaParams['image'] = sprintf(
                '%s%s',
                $this->currentHost,
                $metaParams['image']
            );
        }
    }

    /**
     * Set HTML Canonical Url Meta Tag Value
     *
     * @param array $metaParams Output HTML Meta Tag Values
     * @param array $meta Input HTML Meta Tag Values
     */
    private function _setMetaCanonicalUrl(
        array &$metaParams,
        array $meta
    ): void
    {
        $canonicalUrl = $this->currentUrl;

        if (
            !array_key_exists('canonical_url', $meta) &&
            !empty($meta['canonical_url'])
        ) {
            $canonicalUrl = $meta['canonical_url'];
        }

        $metaParams['canonical_url'] = sprintf(
            '%s%s',
            $this->currentHost,
            (string)$canonicalUrl
        );
    }

    /**
     * Get Page Title From Current Page Path In Site Structure
     *
     * @param array|null $pagePath Current Page Path In Site Structure
     *
     * @return string Page Title
     */
    private function _getTitleByPagePath(?array $pagePath = null): string
    {
        $pagePath = empty($pagePath) ? [] : $pagePath;
        $pagePath = array_reverse($pagePath);
        $pagePath = array_values($pagePath);

        return implode(static::PAGE_TITLE_SEPARATOR, $pagePath);
    }
}
