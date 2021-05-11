<?php
/**
 * Core Controller Class
 */
class ControllerCore extends CommonCore
{
    /**
     * @var string Default Area
     */
    const DEFAULT_AREA = 'default';

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
     * @var int Current Page In Pagination
     */
    public $page = 1;

    /**
     * @var array|null List Of Params From URL
     */
    private $_urlParams = [];

    /**
     * @var Response|null Response Instance
     */
    private $_response = null;

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

        $this->setLanguage($language);

        $this->page = $page < 1 ? 1 : $page;

        $this->_response = new Response();

        $this->execHooks('onAfterControllerInit', $this->commonData);
    }

    /**
     * Return Data In JSON Format
     *
     * @param bool       $status Is Request Successful
     * @param array|null $data   Output Data
     */
    public function returnJson(bool $status = true, ?array $data = null): void
    {
        $this->_response->returnJson($status, $data);
    }

    /**
     * Display Static Page
     *
     * @param string|null $staticPageName Static Page File Name
     * @param string|null $templatePage   Site Template Page Name
     */
    public function displayStaticPage(
        ?string $staticPageName = null,
        ?string $templatePage   = null
    ): void
    {
        if (empty($staticPageName)) {
            $staticPageName = $this->getValueFromUrl('slug');
        }

        $this->_response->displayStaticPage($staticPageName, $templatePage);
    }

    /**
     * Return Data In HTML Format
     *
     * @param string|null $template Teplate Page Name
     * @param array|null  $params   Params Data For Templates
     * @param int         $ttl      Time To Live Of Template Cache
     */
    public function render(
        ?string $template   = null,
        ?array  $params     = null,
        int     $ttl        = 0
    ): void
    {
        if (empty($template)) {
            throw new Exception('Template Page Name Is Empty');
        }

        $params = (array) $params;
        $params = array_merge($params, $this->configData['main']);
        $params = array_merge($params, $this->commonData);

        $this->_response->render($template, $params, $ttl);
    }

    /**
     * Default Error Action
     */
    public function displayError(): void
    {
        $errorCode    = (int) $this->getValueFromUrl('code');
        $errorMessage = $this->_handleHttpError($errorCode);

        $errorMessage = sprintf(
            'HTTP Error #%d (%s)',
            $errorCode,
            $errorMessage
        );

        throw new \Exception($errorMessage);
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

        $this->_response->displayErrorPage($errorMessage, $errorCode);
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
}
