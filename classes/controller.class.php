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
     * @var string|null Current URL
     */
    public $currentUrl = null;

    /**
     * @var string|null Current Host
     */
    public $currentHost = null;

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
        $this->_setPage($page);
        $this->_setLanguage($language);
        $this->_setCurrentUrl();
        $this->_setCurrentHost();
    }

    /**
     * Redirect To URL
     *
     * @param string|null $url         URL Value
     * @param bool        $isPermanent Is Redirect Permanently
     */
    public function redirect(
        ?string $url         = null,
        bool    $isPermanent = false
    ): void
    {
        $url  = empty($url) ? '/' : $url;
        $code = $isPermanent ? 301 : 302;

        header(sprintf('Location: %s', $url), true, $code);

        exit(0);
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
     * @param int|null $error HTTP Error Code
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

        $dataParams = [];

        $dataParams = $this->configData['main'];

        foreach ($this->commonData as $idx => $value) {
            $dataParams[$idx] = $value;
        }

        foreach ($params as $idx => $value) {
            $dataParams[$idx] = $value;
        }

        if (!array_key_exists('pagePath', $dataParams)) {
            $dataParams['pagePath'] = [];
        }

        if (!array_key_exists('meta', $dataParams)) {
            $dataParams['meta'] = [];
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

        $templater->scope = $this->templaterScope;

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
     * @param array $postData|null POST Request Data
     */
    private function _setPostData(?array $postData = null): void
    {
        $securityPlugin = $this->getPlugin('security');

        $escapeMethod = [
            $securityPlugin,
            'escapeInput'
        ];

        if (!empty($postData)) {
            $this->post = array_map($escapeMethod, $postData);
        }
    }

    /**
     * Set GET Request Data
     *
     * @param array $getData|null GET Request Data
     */
    private function _setGetData(?array $getData = null): void
    {
        $securityPlugin = $this->getPlugin('security');

        $escapeMethod = [
            $securityPlugin,
            'escapeInput'
        ];

        if (!empty($getData)) {
            $this->get = array_map($escapeMethod, $getData);
        }
    }

    /**
     * Set Current URL
     *
     * @param array $getData|null GET Request Data
     */
    private function _setCurrentUrl(): void
    {
        $this->currentUrl = '/';

        if (
            array_key_exists('REAL_REQUEST_URI', $_SERVER) &&
            !empty($_SERVER['REAL_REQUEST_URI'])
        ) {
            $this->currentUrl = $_SERVER['REAL_REQUEST_URI'];
        }
    }

    /**
     * Set Current Page In Pagination
     *
     * @param int $page Current Page Number
     */
    private function _setPage(int $page = 1): void
    {
        $this->page = $page < 1 ? 1 : $page;
    }

    /**
     * Set List Of Params From URL
     *
     * @param array|null $urlParams List Of Params From URL
     */
    private function _setUrlParams(?array $urlParams = null): void
    {
        $securityPlugin = $this->getPlugin('security');

        $escapeMethod = [
            $securityPlugin,
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
        if (!empty($language)) {
            $language = $this->_getDefaultLanguage();
        }

        if (!empty($language)) {
            $language = $this->_getDefaultLanguage();
        }

        $this->session->set('language', $language);
        $this->language = $language;
    }

    /**
     * Set Current Host
     */
    private function _setCurrentHost(): void
    {
        $this->currentHost = sprintf(
            '%s://%s',
            $this->configData['main']['site_protocol'],
            $this->configData['main']['site_domain']
        );
    }

    /**
     * Get Default Language
     */
    private function _getDefaultLanguage(): string
    {
        if (defined('DEFAULT_LANGUAGE')) {
            return DEFAULT_LANGUAGE;
        }

        return static::DEFAULT_LANGUAGE;
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

        if (array_key_exists('description', $meta)) {
            $metaData['description'] = $meta['description'];
        }

        if (array_key_exists('image', $meta)) {
            $metaData['image'] = $meta['image'];
        }

        $metaData['image'] = sprintf(
            '%s%s',
            $this->currentHost,
            $metaData['image']
        );

        $metaData['title']       = $mainConfigData['site_name'];
        $metaData['site_name']   = $mainConfigData['site_name'];
        $metaData['site_slogan'] = $mainConfigData['site_slogan'];
        $metaData['locale']      = $mainConfigData['site_locale'];

        $launchYear = date('Y', strtotime($mainConfigData['launch_date']));

        $metaData['copyright'] = sprintf('&copy; %s', $metaData['site_name']);

        $copyrightDate = date('Y');

        if (date('Y') !== $launchYear) {
            $copyrightDate = sprintf('%s-%s', $launchYear, date('Y'));
        }

        $metaData['copyright'] = $copyrightDate;

        if (!empty($pagePath)) {
            $metaData['title'] = sprintf(
                '%s%s%s',
                $this->_getTitleByPagePath($pagePath),
                static::PAGE_TITLE_SEPARATOR,
                $metaData['title']
            );
        }

        if (!array_key_exists('canonical_url', $meta)) {
            $meta['canonical_url'] = null;
        }

        $metaData['canonical_url'] = $this->_getCanonicalFullUrl(
            $meta['canonical_url']
        );

        return $metaData;
    }

    /**
     * Get Canonical Full URL Of Current Page
     *
     * @param array|null $pagePath Current Page Path In Site Structure
     *
     * @return string Canonical Full URL Of Current Page
     */
    private function _getCanonicalFullUrl(?string $canonicalUrl = null): string
    {
        if (!empty($canonicalUrl)) {
            $canonicalUrl = $this->currentUrl;
        }

        return sprintf('%s%s', $this->currentHost, $canonicalUrl);
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
