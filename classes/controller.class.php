<?php
class ControllerCore extends CommonCore {
    const PAGE_TITLE_SEPARATOR = ' / ';

    public $post = [];
    public $URLParam = '';
    public $commonData = [];
    public $configData = [];
    public $templaterScope  = 'site';
    public $page = 1;
    public $isOutputJSON = false;

    public function __construct(
        string $URLParam = '',
        array  $postData = [],
        int    $page     = 1
    )
    {
        session_start();
        $this->_setURLParam($URLParam);
        $this->_setPostData($postData);
        $this->_setPage($page);
        $this->_escapeSessionData();
        $this->_setFlashSessionData();
        $this->_initConfigs();
        $this->_setOutputType();
    }

    private function _setOutputType() : void
    {
        define('OUTPUT_FORMAT_JSON', $this->isOutputJSON);
    }

    private function _setPostData(array $postData = []) : void
    {
        $securityLib = $this->initLib('security');

        $escapeMethod = [
            $securityLib,
            'escapeInput'
        ];

        $this->post = array_map($escapeMethod, $postData);
    }

    private function _setPage(int $page = 1) : void
    {
        $this->page = $page < 1 ? 1 : $page;
    }

    private function _initConfigs() : void
    {
        $this->configData['main'] = $this->initConfig('main');
    }

    private function _escapeSessionData() : void
    {
        $securityLib = $this->initLib('security');

        $escapeMethod = [
            $securityLib,
            'escapeInput'
        ];

        $_SESSION = array_map($escapeMethod, $_SESSION);
    }

    private function _setURLParam(string $URLParam = '') : void
    {
        $securityLib = $this->initLib('security');

        $this->URLParam = $securityLib->escapeInput($URLParam);
    }

    private function _setFlashSessionData()
    {
        if(
            isset($_SESSION['flash_data']) &&
            is_array($_SESSION['flash_data']) &&
            count($_SESSION['flash_data'])>0
        ){
            foreach (
                $_SESSION['flash_data'] as $flashDataIDX => $flashDataVal
            ) {
                $this->commonData[$flashDataIDX] = $flashDataVal;
            }
        }

        $_SESSION['flash_data'] = [];
    }

    public function redirect(
        string $URL = '',
        bool $isPermanent = false
    ) : void
    {
        $URL = strlen($URL) > 0 ? $URL : '/';
        $code = $isPermanent ? 301 : 302;

        header("Location: {$URL}", true, $code);
        exit(0);
    }

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

    public function render(
        string $template = '',
        array $params = [],
        int $ttl = 0
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

        $breadcrumbs = $this->initLib('breadcrumbs');

        $dataParams['breadcrumbs'] = $breadcrumbs->getHTML(
                                        $dataParams['pagePath']
                                     );

        $templater = $this->initLib('templater');

        $templater->scope = $this->templaterScope;

        $templater->render($template, $dataParams, $ttl);
    }

    public function CRUDList(string $modelName = '') : void
    {
        $model = $this->initModel($modelName);
        $modelVOs = $model->getByPage($this->page);

        $this->render($modelName.'/list', [
            $modelName.'List' => $modelVOs
        ]);
    }

    public function CRUDCreate(
        string $modelName = '',
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

    public function CRUDUpdate(
        string $modelName = '',
        string $redirectURI = '/'
    ) : void
    {
        $message = NULL;
        $id = (int) $this->URLParam;

        $model = $this->initModel($modelName);
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

    public function CRUDDelete(
        string $modelName = '',
        string $redirectURI = '/'
    ) : void
    {
        $id = (int) $this->URLParam;
        $model = $this->initModel($modelName);

        if (!$model->removeByID($id)) {
            throw new Exception("Error While Removing {$modelName} #{$id}");
        }

        $this->redirect($redirectURI);
    }

    public function actionError()
    {
        //;
    }

    private function _getMetaParams(
        array $pagePath = [],
        array $meta = []
    ) : array
    {
        $metaData = $this->initConfig('seo');
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

        $metaData['title'] = $mainConfigData['site_name'];
        $metaData['site_name'] = $mainConfigData['site_name'];
        $metaData['site_slogan'] = $mainConfigData['site_slogan'];
        $metaData['locale'] = $mainConfigData['site_locale'];

        $launchYear = date('Y', strtotime($mainConfigData['launch_date']));

        $metaData['copyright'] = '&copy; '.$metaData['site_name'];

        if (date('Y') != $launchYear) {
            $metaData['copyright'] = $metaData['copyright'].' '.
                                     $launchYear.'-'.date('Y');
        } else {
            $metaData['copyright'] = $metaData['copyright'].' '.date('Y');
        }

        if (count($pagePath) > 0) {
            $metaData['title'] = $metaData['title'].
                                 static::PAGE_TITLE_SEPARATOR.
                                 $this->_getTitleByPagePath($pagePath);
        }

        if (!array_key_exists('canonical_url', $meta)) {
            if (
                array_key_exists('REAL_REQUEST_URI', $_SERVER) &&
                strlen($_SERVER['REAL_REQUEST_URI']) > 0
            ) {
                $meta['canonical_url'] = $_SERVER['REAL_REQUEST_URI'];
            } else {
                $meta['canonical_url'] = '/';
            }
        }

        $metaData['canonical_url'] = $mainConfigData['site_protocol'].'://'.
                                     $mainConfigData['site_domain'].
                                     $meta['canonical_url'];

        return $metaData;
    }

    private function _getTitleByPagePath(array $pagePath = []) : string
    {
        $pagePath = array_reverse($pagePath);
        $pagePath = array_values($pagePath);

        return implode(static::PAGE_TITLE_SEPARATOR, $pagePath);
    }
}
?>