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
    public $isOutputJSON = FALSE;

    public function __construct(
        string $URLParam = '',
        array  $postData = [],
        int    $page     = 1
    )
    {
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
        $securityPlugin = $this->initPlugin('security');

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
        $this->configData['main'] = $this->initConfig('main');
    }

    /**
     * Set Param Data From URL
     *
     * @param string $URLParam Param Data From URL
     */
    private function _setURLParam(string $URLParam = '') : void
    {
        $securityPlugin = $this->initPlugin('security');

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
        string $url         = '',
        bool   $isPermanent = FALSE
    ) : void
    {
        $url  = strlen($url) > 0 ? $url : '/';
        $code = $isPermanent ? 301 : 302;

        header("Location: {$url}", TRUE, $code);
        exit(0);
    }

    /**
     * Return Data In JSON Format
     *
     * @param bool  $status Is Request Successful
     * @param array $data   Output Data
     */
    public function returnJSON(bool $status = TRUE, array $data = []) : void
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
        array  $params   = [],
        int    $ttl      = 0
    ) : void
    {
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

        $breadcrumbs = $this->initPlugin('breadcrumbs');

        $dataParams['breadcrumbs'] = $breadcrumbs->getHTML(
                                        $dataParams['pagePath']
                                     );

        $templater = $this->initPlugin('templater');

        $templater->scope = $this->templaterScope;

        $templater->render($template, $dataParams, $ttl);
    }

    /**
     * Display List By CRUD Action
     *
     * @param string $modelName Name Of Model Class
     */
    public function CRUDList(string $modelName = '') : void
    {
        $model = $this->initModel($modelName);
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
        string $modelName   = '',
        string $redirectURI = '/'
    ) : void
    {
        $message = NULL;

        $model = $this->initModel($modelName);

        if (count($this->post) > 0) {
            list($res, $message) = $model->formHandler($this->post);
            if ($res) {
                $this->redirect($redirectURI);
            }
        }

        $formAction = $modelName.'Form';
        $this->$formAction(NULL, $message);
    }

    /**
     * Update New Item By CRUD Action
     *
     * @param string $modelName   Name Of Model Class
     * @param string $redirectURI URI For Redirection After Updation
     */
    public function CRUDUpdate(
        string $modelName   = '',
        string $redirectURI = '/'
    ) : void
    {
        $message = NULL;
        $id = (int) $this->URLParam;

        $model   = $this->initModel($modelName);
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
     * Remove New Item By CRUD Action
     *
     * @param string $modelName   Name Of Model Class
     * @param string $redirectURI URI For Redirection After Removal
     */
    public function CRUDDelete(
        string $modelName   = '',
        string $redirectURI = '/'
    ) : void
    {
        $id    = (int) $this->URLParam;
        $model = $this->initModel($modelName);

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
        array $meta     = []
    ) : array
    {
        $metaData       = $this->initConfig('seo');
        $mainConfigData = $this->initConfig('main');

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

        if (date('Y') !== $launchYear) {
            $metaData['copyright'] = $metaData['copyright'].' '.
                                     $launchYear.'-'.date('Y');
        } else {
            $metaData['copyright'] = $metaData['copyright'].' '.date('Y');
        }

        if (count($pagePath) > 0) {
            $metaData['title'] = $this->_getTitleByPagePath($pagePath).
                                 static::PAGE_TITLE_SEPARATOR.
                                 $metaData['title'];
        }

        if (!array_key_exists('canonical_url', $meta)) {
            if (
                $this->serverInfo->has('REAL_REQUEST_URI') &&
                strlen($this->serverInfo->get('REAL_REQUEST_URI')) > 0
            ) {
                $meta['canonical_url'] = $this->serverInfo->get(
                    'REAL_REQUEST_URI'
                );
            } else {
                $meta['canonical_url'] = '/';
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
