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
     * @var array POST Request Data
     */
    public $post = null;

    /**
     * @var string Param Data From URL
     */
    public $URLParam = null;

    /**
     * @var array Common Data For Templates, Cross-Model And Cross-Plugin Usage
     */
    public $commonData = [];

    /**
     * @var array Data From JSON Config Files
     */
    public $configData = [];

    /**
     * @var string HTML Termplates Scope
     */
    public $templaterScope = 'site';

    /**
     * @var int Current Page In Pagination
     */
    public $page = 1;

    /**
     * @var bool Is Output In JSON Format
     */
    public $isOutputJSON = false;

    public function __construct(
        ?string $URLParam = null,
        ?array  $postData = null,
        int     $page = 1
    ) {
        session_start();

        parent::__construct();

        $this->_setURLParam($URLParam);
        $this->_setPostData($postData);
        $this->_setPage($page);
        $this->_setFlashSessionData();
        $this->_initConfigs();
        $this->_setOutputType();
    }

    public function actionError(): void
    {
        $errorCode    = (int) $this->URLParam;
        $errorMessage = $this->handleHttpError($errorCode);

        $errorMessage = sprintf(
            'HTTP Error #%d (%s)',
            $errorCode,
            $errorMessage
        );

        throw new Exception($errorMessage);
    }

    /**
     * Handle HTTP Error Page
     */
    public function handleHttpError(?int $errorCode = null): string
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
     * Set Output Format Type
     */
    private function _setOutputType(): void
    {
        define('OUTPUT_FORMAT_JSON', $this->isOutputJSON);
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
     * Set Current Page In Pagination
     *
     * @param int $page Current Page Number
     */
    private function _setPage(int $page = 1): void
    {
        $this->page = $page < 1 ? 1 : $page;
    }

    /**
     * Set Data From JSON Config Files
     */
    private function _initConfigs(): void
    {
        $this->configData['main']  = $this->getConfig('main');
        $this->configData['hooks'] = $this->getConfig('hooks');
    }

    /**
     * Set Param Data From URL
     *
     * @param string|null $URLParam Param Data From URL
     */
    private function _setURLParam(?string $URLParam = null): void
    {
        $securityPlugin = $this->getPlugin('security');

        $this->URLParam = $securityPlugin->escapeInput($URLParam);
    }

    /**
     * Set Data From One Use Session Param To Common Data
     */
    private function _setFlashSessionData(): void
    {
        if (
            $this->session->has('flash_data') &&
            is_array($this->session->get('flash_data'))
        ) {
            $flashData = $this->session->get('flash_data');
            foreach ($flashData as $flashDataIDX => $flashDataVal) {
                $this->commonData[$flashDataIDX] = $flashDataVal;
            }
        }

        $this->session->set('flash_data', []);
    }

    /**
     * Redirect To URL
     *
     * @param string|null $url         URL Value
     * @param bool        $isPermanent Is Redirect Permanently
     */
    public function redirect(
        ?string $url = null,
        bool    $isPermanent = false
    ): void {
        $url  = empty($url) ? '/' : $url;
        $code = $isPermanent ? 301 : 302;

        header("Location: {$url}", true, $code);

        exit(0);
    }

    /**
     * Return Data In JSON Format
     *
     * @param bool       $status Is Request Successful
     * @param array|null $data   Output Data
     */
    public function returnJSON(bool $status = true, ?array $data = null): void
    {
        $dataJSON = [
            'status' => $status,
            'data'   => $data
        ];
        $dataJSON = json_encode($dataJSON);

        header('Content-Type: application/json');
        echo $dataJSON;

        exit(0);
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
        int     $ttl = 0
    ): void {
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

        $dataParams['breadcrumbs'] = $breadcrumbs;

        $templater = $this->getPlugin('templater');

        $templater->scope = $this->templaterScope;

        $dataParams = $this->execHook('onBeforeRender', $dataParams);

        $templater->render($template, $dataParams, $ttl);
    }

    /**
     * Execute All Hooks In Scope
     *
     * @param string|null $hookScope  Scope Of Hooks
     * @param array|null  $entityData Entity Data
     */
    public function execHook(
        ?string $hookScope = null,
        ?array  $entityData = null
    ): array {
        $hooksData = $this->configData['hooks'];

        $entityData = empty($entityData) ? [] : $entityData;

        if (!array_key_exists($hookScope, $hooksData)) {
            return $entityData;
        }

        foreach ($hooksData[$hookScope] as $hookItem) {
            if (!array_key_exists('hook', $hookItem)) {
                continue;
            }

            if (!array_key_exists('method', $hookItem)) {
                continue;
            }

            $hookClass        = $hookItem['hook'];
            $hookFile         = $hookItem['hook'];
            $hookAutoloadFile = $hookItem['hook'];
            $hookMethod       = $hookItem['method'];

            $hookClass = mb_convert_case($hookClass, MB_CASE_TITLE).'Hook';
           
            $hookFile = __DIR__.'/../../hooks/'.$hookFile.'/'.$hookFile.'.php';

            $hookAutoloadFile = __DIR__.'/../../hooks/'.
                                $hookAutoloadFile.'/autoload.php';

            if (file_exists($hookAutoloadFile) && is_file($hookAutoloadFile)) {
                $hookFile = $hookAutoloadFile;
            }

            if (!file_exists($hookFile) || !is_file($hookFile)) {
                throw new Exception('Hook '.$hookItem['hook'].' Is Not Exists');
            }

            require_once $hookFile;

            if (!class_exists($hookClass)) {
                throw new Exception('Hook Class '.$hookClass.' Is Not Exists');
            }

            $hookInstance = new $hookClass($entityData);

            if (!method_exists($hookInstance, $hookMethod)) {
                $errorMessage = 'Hook Method '.$hookMethod.
                                ' In Class '.$hookClass.' Is Not Exists';
                throw new Exception($errorMessage);
            }

            $hookInstance->$hookMethod();

            $entityData = $hookInstance->getEntity();
        }

        return $entityData;
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
        ?array $meta = null
    ): array {
        $pagePath = empty($pagePath) ? [] : $pagePath;
        $meta     = empty($meta) ? [] : $meta;

        $metaData       = $this->getConfig('seo');
        $mainConfigData = $this->getConfig('main');

        if (array_key_exists('description', $meta)) {
            $metaData['description'] = $meta['description'];
        }

        if (array_key_exists('image', $meta)) {
            $metaData['image'] = $meta['image'];
        }

        $metaData['image'] = $mainConfigData['site_protocol'].'://'.
                             $mainConfigData['site_domain'].
                             $metaData['image'];

        $metaData['title']       = $mainConfigData['site_name'];
        $metaData['site_name']   = $mainConfigData['site_name'];
        $metaData['site_slogan'] = $mainConfigData['site_slogan'];
        $metaData['locale']      = $mainConfigData['site_locale'];

        $launchYear = date('Y', strtotime($mainConfigData['launch_date']));

        $metaData['copyright'] = '&copy; '.$metaData['site_name'];

        $copyrightDate = date('Y');

        if (date('Y') !== $launchYear) {
            $copyrightDate = $launchYear.'-'.date('Y');
        }

        $metaData['copyright'] = $copyrightDate;

        if (count($pagePath) > 0) {
            $metaData['title'] = $this->_getTitleByPagePath($pagePath).
                                 static::PAGE_TITLE_SEPARATOR.
                                 $metaData['title'];
        }

        if (!array_key_exists('canonical_url', $meta)) {
            $meta['canonical_url'] = '/';

            if (
                $this->serverInfo->has('REAL_REQUEST_URI') &&
                strlen($this->serverInfo->get('REAL_REQUEST_URI')) > 0
            ) {
                $meta['canonical_url'] = $this->serverInfo->get(
                    'REAL_REQUEST_URI'
                );
            }
        }

        $metaData['canonical_url'] = $mainConfigData['site_protocol'].'://'.
                                     $mainConfigData['site_domain'].
                                     $meta['canonical_url'];

        return $metaData;
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
