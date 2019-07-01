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
    public $post = [];

    /**
     * @var string Param Data From URL
     */
    public $URLParam = '';

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
        string $URLParam = '',
        array  $postData = [],
        int    $page = 1
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

    /**
     * Set Output Format Type
     */
    private function _setOutputType() : void
    {
        define('OUTPUT_FORMAT_JSON', $this->isOutputJSON);
    }

    /**
     * Set POST Request Data
     *
     * @param array $postData POST Request Data
     */
    private function _setPostData(array $postData = []) : void
    {
        $securityPlugin = $this->getPlugin('security');

        $escapeMethod = [
            $securityPlugin,
            'escapeInput'
        ];

        $this->post = array_map($escapeMethod, $postData);
    }

    /**
     * Set Current Page In Pagination
     *
     * @param int $page Current Page Number
     */
    private function _setPage(int $page = 1) : void
    {
        $this->page = $page < 1 ? 1 : $page;
    }

    /**
     * Set Data From JSON Config Files
     */
    private function _initConfigs() : void
    {
        $this->configData['main'] = $this->getConfig('main');
        $this->configData['hooks'] = $this->getConfig('hooks');
    }

    /**
     * Set Param Data From URL
     *
     * @param string $URLParam Param Data From URL
     */
    private function _setURLParam(string $URLParam = '') : void
    {
        $securityPlugin = $this->getPlugin('security');

        $this->URLParam = $securityPlugin->escapeInput($URLParam);
    }

    /**
     * Set Data From One Use Session Param To Common Data
     */
    private function _setFlashSessionData() : void
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
     * @param string $url         URL Value
     * @param bool   $isPermanent Is Redirect Permanently
     */
    public function redirect(
        string $url = '',
        bool   $isPermanent = false
    ) : void {
        $url  = strlen($url) > 0 ? $url : '/';
        $code = $isPermanent ? 301 : 302;

        header("Location: {$url}", true, $code);
        exit(0);
    }

    /**
     * Return Data In JSON Format
     *
     * @param bool  $status Is Request Successful
     * @param array $data   Output Data
     */
    public function returnJSON(bool $status = true, array $data = []) : void
    {
        $dataJSON = [
            'status' => $status,
            'data' => $data
        ];
        $dataJSON = json_encode($dataJSON);

        header('Content-Type: application/json');
        echo $dataJSON;
        exit(0);
    }

    /**
     * Return Data In HTML Format
     *
     * @param string $template Teplate Page Name
     * @param array  $params   Params Data For Templates
     * @param int    $ttl      Time To Live Of Template Cache
     */
    public function render(
        string $template = '',
        array  $params = [],
        int    $ttl = 0
    ) : void {
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

        $dataParams['breadcrumbs'] = $breadcrumbs->getHTML(
                                        $dataParams['pagePath']
                                     );

        $templater = $this->getPlugin('templater');

        $templater->scope = $this->templaterScope;

        $dataParams = $this->execHook('onBeforeRender', $dataParams);

        $templater->render($template, $dataParams, $ttl);
    }

    /**
     * Display List By CRUD Action
     *
     * @param string $modelName Name Of Model Class
     */
    public function CRUDList(string $modelName = '') : void
    {
        $model    = $this->getModel($modelName);
        $modelVOs = $model->getByPage($this->page);

        $this->render($modelName.'/list', [
            $modelName.'List' => $modelVOs
        ]);
    }

    /**
     * Create New Item By CRUD Action
     *
     * @param string $modelName   Name Of Model Class
     * @param string $redirectURI URI For Redirection After Creation
     */
    public function CRUDCreate(
        string $modelName = '',
        string $redirectURI = '/'
    ) : void {
        $message = null;

        $model = $this->getModel($modelName);

        if (count($this->post) > 0) {
            list($res, $message) = $model->formHandler($this->post);
            if ($res) {
                $this->redirect($redirectURI);
            }
        }

        $formAction = $modelName.'Form';
        $this->$formAction(null, $message);
    }

    /**
     * Update New Item By CRUD Action
     *
     * @param string $modelName   Name Of Model Class
     * @param string $redirectURI URI For Redirection After Updation
     */
    public function CRUDUpdate(
        string $modelName = '',
        string $redirectURI = '/'
    ) : void {
        $message = null;

        $id = (int) $this->URLParam;

        $model   = $this->getModel($modelName);
        $modelVO = $model->getByID($id);

        if (!$modelVO->has('id')) {
            throw new Exception('Invalid Model ID');
        }

        if (count($this->post) > 0) {
            list($res, $message) = $model->formHandler($this->post, $id);
            if ($res) {
                $this->redirect($redirectURI);
            }
        }

        $formAction = $modelName.'Form';
        $this->$formAction($modelVO, $message);
    }


    /**
     * Execute All Hooks In Scope
     *
     * @param string $hookScope  Scope Of Hooks
     * @param array  $entityData Entity Data
     */
    public function execHook(
        string $hookScope = '',
        array  $entityData = []
    ) : array {
        $hooksData = $this->configData['hooks'];

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
     * Remove New Item By CRUD Action
     *
     * @param string $modelName   Name Of Model Class
     * @param string $redirectURI URI For Redirection After Removal
     */
    public function CRUDDelete(
        string $modelName = '',
        string $redirectURI = '/'
    ) : void {
        $id    = (int) $this->URLParam;
        $model = $this->getModel($modelName);

        if (!$model->removeByID($id)) {
            throw new Exception("Error While Removing {$modelName} #{$id}");
        }

        $this->redirect($redirectURI);
    }

    /**
     * Get HTML Meta Tag Values
     *
     * @param array $pagePath Current Page Path In Site Structure
     * @param array $meta     Input HTML Meta Tag Values
     *
     * @return array Output HTML Meta Tag Values
     */
    private function _getMetaParams(
        array $pagePath = [],
        array $meta = []
    ) : array {
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
     * @param array $pagePath Current Page Path In Site Structure
     *
     * @return string Page Title
     */
    private function _getTitleByPagePath(array $pagePath = []) : string
    {
        $pagePath = array_reverse($pagePath);
        $pagePath = array_values($pagePath);

        return implode(static::PAGE_TITLE_SEPARATOR, $pagePath);
    }
}
?>
