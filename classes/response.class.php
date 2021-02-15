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
     * @var string HTML Termplates Scope
     */
    private $_templaterScope = 'site';

    public function __construct(?string $templaterScope = null)
    {
        parent::__construct();

        if (!empty($templaterScope)) {
            $this->_templaterScope = $templaterScope;
        }
    }

    /**
     * Return Data In JSON Format
     *
     * @param bool       $status Is Request Successful
     * @param array|null $data   Output Data
     */
    public function returnJson(bool $status = true, ?array $data = null): void
    {
        $jsonData = [
            'status' => $status,
            'data'   => $data
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
     * @param string|null $templatePage   Site Template Page Name
     * @param string|null $notFoundUrl    Not Found Page URL
     */
    public function displayStaticPage(
        ?string $staticPageName = null,
        ?string $templatePage   = null,
        ?string $notFoundUrl    = null
    ): void
    {
        $pagePlugin = $this->getPlugin('page');

        if (empty($templatePage)) {
            $templatePage = PagePlugin::DEFAULT_TEMPLATE_PAGE;
        }

        $staticPageVO = $pagePlugin->getVO(
            $staticPageName,
            $this->_templaterScope,
            $templatePage,
            $notFoundUrl
        );

        $pagePath = [
            '#' => $staticPageVO->getTitle()
        ];

        $this->render(
            $templatePage,
            [
                'staticPage' => $staticPageVO,
                'pagePath'   => $pagePath
            ]
        );
    }

    /**
     * Display Error Page
     *
     * @param string $errorMessage HTTP Error Message
     * @param int    $errorCode HTTP Error Code
     */
    public function displayErrorPage(
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
                'errorCode'    => $errorCode,
                'pagePath'     => $pagePath
            ]
        );
    }

    /**
     * Return Data In HTML Format
     *
     * @param string|null $template Teplate Page Name
     * @param array|null  $params   Params Data For Templates
     * @param int         $ttl      Time To Live Of Template Cache
     */
    public function render(
        ?string $template = null,
        ?array  $params = null,
        int     $ttl      = 0
    ): void
    {
        $params = (array) $params;

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

        $breadcrumbs = $this->getPlugin('breadcrumbs');
        $breadcrumbs = $breadcrumbs->getHTML($params['pagePath']);

        $params['breadcrumbs']     = $breadcrumbs;
        $params['currentLanguage'] = $this->language;
        $params['currentUrl']      = $this->currentUrl;

        $templater = $this->getPlugin('templater');

        $templater->setScope($this->_templaterScope);

        $this->execHooks('onBeforeRender', $params);

        $templater->render($template, $params, $ttl);
    }

    /**
     * Get HTML Meta Tag Values
     *
     * @param array|null $pagePath Current Page Path In Site Structure
     * @param array|null $meta     Input HTML Meta Tag Values
     *
     * @return array Output HTML Meta Tag Values
     */
    private function _getMetaParams(
        ?array $pagePath = null,
        ?array $meta     = null
    ): array
    {
        $pagePath = (array) $pagePath;
        $meta     = (array) $meta;

        $metaParams     = $this->configData['seo'];
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

        $metaParams['title']       = $mainConfigData['site_name'];
        $metaParams['site_name']   = $mainConfigData['site_name'];
        $metaParams['site_slogan'] = $mainConfigData['site_slogan'];
        $metaParams['locale']      = $mainConfigData['site_locale'];

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
     * @param array $metaParams     Output HTML Meta Datas
     * @param array $mainConfigData Main Config Values
     */
    private function _setMetaParamsCopyright(
        array &$metaParams,
        array $mainConfigData
    ): void
    {
        $launchYear = date('Y', strtotime($mainConfigData['launch_date']));
        $siteName   = null;

        if (!array_key_exists('site_name', $metaParams)) {
            $siteName = $metaParams['site_name'];
        }

        $metaParams['copyright'] = sprintf('&copy; %s', (string) $siteName);
        $copyrightDate           = date('Y');

        if (date('Y') !== $launchYear) {
            $copyrightDate = sprintf('%s-%s', $launchYear, date('Y'));
        }

        $metaParams['copyright'] = $copyrightDate;
    }

    /**
     * Set HTML Image Meta Tag Value
     *
     * @param array $metaParams Output HTML Meta Tag Values
     * @param array $meta       Input HTML Meta Tag Values
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
     * @param array $meta       Input HTML Meta Tag Values
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
            (string) $canonicalUrl
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
