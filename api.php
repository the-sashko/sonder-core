<?php
/**
 * Main API Class
 */
class API extends App
{
    use Router;

    public function __construct()
    {
        parent::__construct();

        $this->_replaceUrl();
    }

    /**
     * Main Method For API
     */
    public function run(): void
    {
        list($model, $action) = $this->_parseUrl();

        require_once __DIR__.'/classes/controller.api.class.php';

        try {
            $controller = new ApiControllerCore($model, $action);
            $controller->execute($action);
        } catch (Exception $exp) {
            $this->exceptionHandler($exp);
        }

        exit(0);
    }

    /**
     * Rewrite URL By Rules
     */
    private function _replaceUrl(): void
    {
        $url = $_SERVER['REQUEST_URI'];

        $url = preg_replace('/^\/api\/(.*?)$/sui', '/$1', $url);

        if (preg_match('/^\/(.*?)\/page-([0-9]+)\/$/su', $url)) {
            $_GET['page'] = preg_replace(
                '/^\/(.*?)\/page-(.*?)\/$/su',
                '$2',
                $url
            );

            $url = preg_replace('/^\/(.*?)\/page-(.*?)\/$/su', '$1', $url);
        }

        $_SERVER['REQUEST_URI'] = $url;
    }

    /**
     * Parse Controller, Method Of Cotroller And Params From URL
     */
    private function _parseUrl(): ?array
    {
        $model  = null;
        $action = null;

        $urlParams = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        parse_str($urlParams, $urlParams);

        if (!empty($urlParams)) {
            $_GET = array_merge($_GET, $urlParams);
        }

        $_SERVER['REQUEST_URI'] = parse_url(
            $_SERVER['REQUEST_URI'],
            PHP_URL_PATH
        );

        $url = $_SERVER['REQUEST_URI'];

        $url = preg_replace('/((^\/)|(\/$))/su', '', $url);

        $urlData = explode('/', $url);

        if (!empty($urlData)) {
            $model = array_shift($urlData);
        }

        if (!empty($urlData)) {
            $action = array_shift($urlData);
        }

        if (null === $model) {
            throw new CoreException(
                CoreException::MESSAGE_CORE_MODEL_IS_NOT_SET,
                CoreException::CODE_CORE_MODEL_IS_NOT_SET
            );
        }

        if (null === $action) {
            throw new CoreException(
                CoreException::MESSAGE_CORE_ACTION_MODEL_IS_NOT_SET,
                CoreException::CODE_CORE_ACTION_MODEL_IS_NOT_SET
            );
        }

        $model  = mb_convert_case($model, MB_CASE_LOWER);
        $action = 'action'.mb_convert_case($action, MB_CASE_TITLE);

        return [
            $model,
            $action
        ];
    }
}
