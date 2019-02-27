<?php
class ControllerCore extends CommonCore {

    public $post = [];
    public $URLParam = '';
    public $commonData = [];
    public $configData = [];
    public $templaterScope  = 'site';
    public $page = 1;

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

        $templater = $this->initLib('templater');

        $templater->scope = $this->templaterScope;

        $templater->render($template, $dataParams, $ttl);
    }

    public function CRUDList(string $modelName = '') : void
    {
        $model = $this->initModel($modelName);
        $modelVOs = $model->getByPage($this->page);

        $this->render($modelName.'/list', [
            $modelName => $modelVOs
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
            list($res, $message) = $model->create($this->post);
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
            list($res, $message) = $model->updateByID($this->post, $id);
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
}
?>