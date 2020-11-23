<?php
/**
 * Core Controller Class
 */
class ControllerCore extends CommonCore
{
    /**
     * @var string Separator For Web Page Title Sections
     */
    const PAGE_TITLE_SEPARATOR = ' / ';

    /**
     * @var string Default Language Of Application
     */
    const DEFAULT_LANGUAGE = 'en';

    /**
     * @var string Default Teplate Page For Static Pages
     */
    const DEFAULT_STATIC_PAGE_TEMPLATE = 'page';

    /**
     * @var string Default Teplate Page For Error Pages
     */
    const DEFAULT_ERROR_PAGE_TEMPLATE = 'error';

    /**
     * @var array|null POST Request Data
     */
    public $post = null;

    /**
     * @var array|null GET Request Data
     */
    public $get = null;

    /**
     * @var array Common Data For Templates, Cross-Model And Cross-Plugin Usage
     */
    public $commonData = [];

    /**
     * @var string HTML Termplates Scope
     */
    public $templaterScope = 'site';

    /**
     * @var int Current Page In Pagination
     */
    public $page = 1;

    /**
     * @var string|null Current Language Of User
     */
    public $language = null;

    /**
     * @var array|null List Of Params From URL
     */
    private $_urlParams = [];

    public function __construct(
        ?array  $urlParams = null,
        int     $page      = 1,
        ?string $language  = null
    )
    {
        session_start();

        parent::__construct();

        $this->_setUrlParams($urlParams);
        $this->_setPostData($_POST);
        $this->_setGetData($_GET);
        $this->_setLanguage($language);

        $this->page = $page < 1 ? 1 : $page;
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
     * Default Error Action
     */
    public function actionError(): void
    {
        $errorCode    = (int) $this->getValueFromUrl('code');
        $errorMessage = $this->_handleHttpError($errorCode);

        $errorMessage = sprintf(
            'HTTP Error #%d (%s)',
            $errorCode,
            $errorMessage
        );

        throw new Exception($errorMessage);
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

        if (empty($staticPageName)) {
            $staticPageName = $this->getValueFromUrl('slug');
        }

        if (empty($templatePage)) {
            $templatePage = PagePlugin::DEFAULT_TEMPLATE_PAGE;
        }

        $staticPageVO = $pagePlugin->getVO(
            $staticPageName,
            $this->templaterScope,
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
     * @param int|null $errorCode HTTP Error Code
     */
    public function displayErrorPage(?int $errorCode = null): void
    {
        if (empty($errorCode)) {
            $errorCode = (int) $this->getValueFromUrl('code');
        }

        $errorMessage = $this->_handleHttpError($errorCode);

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
     * Get Value From URL Params By Name
     *
     * @param string|null $valueName Name Of Value
     *
     * @return string|null Value
     */
    public function getValueFromUrl(?string $valueName = null): ?string
    {
        if (empty($valueName)) {
            return null;
        }

        if (!array_key_exists($valueName, $this->_urlParams)) {
            return null;
        }

        $value = $this->_urlParams[$valueName];

        if (empty($value) || !is_scalar($value)) {
            return null;
        }

        return (string) $value;
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
        ?array  $params   = null,
        int     $ttl      = 0
    ): void
    {
        if (empty($template)) {
            throw new Exception('Template Page Name Is Empty');
        }

        $params = empty($params) ? [] : $params;

        $dataParams = [
            'pagePath' => [],
            'meta'     => []
        ];

        foreach ($this->configData['main'] as $key => $value) {
            $dataParams[$key] = $value;
        }

        foreach ($this->commonData as $key => $value) {
            $dataParams[$key] = $value;
        }

        foreach ($params as $key => $value) {
            $dataParams[$key] = $value;
        }

        $dataParams['meta'] = $this->_getMetaParams(
            $dataParams['pagePath'],
            $dataParams['meta']
        );

        $breadcrumbs = $this->getPlugin('breadcrumbs');
        $breadcrumbs = $breadcrumbs->getHTML($dataParams['pagePath']);

        $dataParams['breadcrumbs']     = $breadcrumbs;
        $dataParams['currentLanguage'] = $this->language;
        $dataParams['currentUrl']      = $this->currentUrl;

        $templater = $this->getPlugin('templater');

        $templater->setScope($this->templaterScope);

        $this->execHooks('onBeforeRender', $dataParams);

        $templater->render($template, $dataParams, $ttl);
    }

    /**
     * Handle HTTP Error Page
     *
     * @return string HTTP Error Message
     */
    private function _handleHttpError(?int $errorCode = null): string
    {
        if (empty($errorCode)) {
            $this->redirect('/', true);
        }

        $errorPlugin = $this->getPlugin('error');

        if (!$errorPlugin->handleHttpError($errorCode)) {
            $this->redirect('/', true);
        }

        return $errorPlugin->getHttpErrorMessage($errorCode);
    }

    /**
     * Set POST Request Data
     *
     * @param array|null $postData POST Request Data
     */
    private function _setPostData(?array $postData = null): void
    {
        $escapeMethod = [
            $this->getPlugin('security'),
            'escapeInput'
        ];

        if (!empty($postData)) {
            $this->post = array_map($escapeMethod, $postData);
        }
    }

    /**
     * Set GET Request Data
     *
     * @param array|null $getData GET Request Data
     */
    private function _setGetData(?array $getData = null): void
    {
        $escapeMethod = [
            $this->getPlugin('security'),
            'escapeInput'
        ];

        if (!empty($getData)) {
            $this->get = array_map($escapeMethod, $getData);
        }
    }

    /**
     * Set List Of Params From URL
     *
     * @param array|null $urlParams List Of Params From URL
     */
    private function _setUrlParams(?array $urlParams = null): void
    {
        $escapeMethod = [
            $this->getPlugin('security'),
            'escapeInput'
        ];

        if (!empty($urlParams)) {
            $this->_urlParams = array_map($escapeMethod, $urlParams);
        }
    }

    /**
     * Set Laguage Value To Session
     */
    private function _setLanguage(?string $language = null): void
    {
        $defaultLanguage = static::DEFAULT_LANGUAGE;

        if (defined('DEFAULT_LANGUAGE')) {
            $defaultLanguage = DEFAULT_LANGUAGE;
        }

        if (empty($language)) {
            $language = $defaultLanguage;
        }

        $this->session->set('language', $language);
        $this->language = $language;
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

        $metaData       = $this->configData['seo'];
        $mainConfigData = $this->configData['main'];

        if (!array_key_exists('image', $metaData)) {
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
            $metaData['description'] = $meta['description'];
        }

        $metaData['title']       = $mainConfigData['site_name'];
        $metaData['site_name']   = $mainConfigData['site_name'];
        $metaData['site_slogan'] = $mainConfigData['site_slogan'];
        $metaData['locale']      = $mainConfigData['site_locale'];

        if (!empty($pagePath)) {
            $metaData['title'] = sprintf(
                '%s%s%s',
                $this->_getTitleByPagePath($pagePath),
                static::PAGE_TITLE_SEPARATOR,
                $metaData['title']
            );
        }

        $this->_setMetaParamsImage($metaData, $meta);
        $this->_setMetaCanonicalUrl($metaData, $meta);
        $this->_setMetaParamsCopyright($metaData, $mainConfigData);

        return $metaData;
    }

    /**
     * Set HTML Copyright Value
     *
     * @param array $metaData       Output HTML Meta Datas
     * @param array $mainConfigData Main Config Values
     */
    private function _setMetaParamsCopyright(
        array &$metaData,
        array $mainConfigData
    ): void
    {
        $launchYear = date('Y', strtotime($mainConfigData['launch_date']));
        $siteName   = null;

        if (!array_key_exists('site_name', $metaData)) {
            $siteName = $metaData['site_name'];
        }

        $metaData['copyright'] = sprintf('&copy; %s', (string) $siteName);
        $copyrightDate         = date('Y');

        if (date('Y') !== $launchYear) {
            $copyrightDate = sprintf('%s-%s', $launchYear, date('Y'));
        }

        $metaData['copyright'] = $copyrightDate;
    }

    /**
     * Set HTML Canonical Url Meta Tag Value
     *
     * @param array $metaData Output HTML Meta Tag Values
     * @param array $meta     Input HTML Meta Tag Values
     */
    private function _setMetaCanonicalUrl(array &$metaData, array $meta): void
    {
        if (array_key_exists('image', $meta)) {
            $metaData['image'] = $meta['image'];
        }

        if (
            array_key_exists('image', $metaData) &&
            !empty($metaData['image'])
        ) {
            $metaData['image'] = sprintf(
                '%s%s',
                $this->currentHost,
                $metaData['image']
            );
        }
    }

    /**
     * Set HTML Image Meta Tag Value
     *
     * @param array $metaData Output HTML Meta Tag Values
     * @param array $meta     Input HTML Meta Tag Values
     */
    private function _setMetaParamsImage(array &$metaData, array $meta): void
    {
        $canonicalUrl = $this->currentUrl;

        if (
            !array_key_exists('canonical_url', $meta) &&
            !empty($meta['canonical_url'])
        ) {
            $canonicalUrl = $meta['canonical_url'];
        }

        $metaData['canonical_url'] = sprintf(
            '%s%s',
            $this->currentHost,
            (string) $meta['canonical_url']
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
