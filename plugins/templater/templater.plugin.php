<?php
/**
 * Plugin For Rendering Templates
 */
class TemplaterPlugin
{
    /**
     * @var string Default Template Page
     */
    const DEFAULT_TEMPLATE = 'main';

    /**
     * @var string Default Scope
     */
    const DEFAULT_SCOPE = 'default';

    /**
     * @var string Templates Directory Path
     */
    const TEMPLATE_DIR = __DIR__.'/../../../res/tpl';

    /**
     * @var string Cache Directory Path
     */
    const CACHE_DIR_PATH = __DIR__.'/../../../res/cache/tpl';

    /**
     * @var string Templates Scope
     */
    private $_scope = null;

    /**
     * @var string Template File Name
     */
    private $_template = null;

    /**
     * Generate And Display HTML Page From Template File
     *
     * @param string|null $template   Template File Name
     * @param array|null  $dataParams Array Of Values For Using In Template
     *                                Page
     * @param int         $ttl        Time To Live Template Cache
     */
    public function render(
        ?string $template   = null,
        ?array  $dataParams = null,
        int     $ttl        = 0
    ): void
    {
        if (empty($template)) {
            $template = static::DEFAULT_TEMPLATE;
        }

        if (empty($this->_scope)) {
            $this->_scope = static::DEFAULT_SCOPE;
        }

        $this->_template = $template;

        $GLOBALS['template_dir']    = static::TEMPLATE_DIR;
        $GLOBALS['template_params'] = (array) $dataParams;
        $GLOBALS['template_scope']  = $this->_scope;
        $GLOBALS['template_ttl']    = $ttl;

        if ($ttl > 0) {
            $this->_setCacheDir();
        }

        foreach ($GLOBALS['template_params'] as $param => $value) {
            $$param = $value;
        }

        $this->_includeTemplate();

        exit(0);
    }

    /**
     * Set Template Scope
     */
    public function setScope(?string $scope = null): void
    {
        if (empty($scope)) {
            $scope = static::DEFAULT_SCOPE;
        }

        $this->_scope = $scope;
    }

    /**
     * Generate And Display HTML Page From Template File
     */
    public function _setCacheDir(): void
    {
        if (empty($this->_scope)) {
            $this->_scope = static::DEFAULT_SCOPE;
        }

        $cacheSlug = sprintf(
            '%s_%s',
            hash('md5', $_SERVER['REQUEST_URI']),
            hash('sha256', $_SERVER['REQUEST_URI'])
        );

        $cacheDir = sprintf(
            '%s/%s/%s/%s',
            static::CACHE_DIR_PATH,
            $cacheSlug,
            $this->_scope,
            $this->_template
        );

        if (!file_exists($cacheDir) || !is_dir($cacheDir)) {
            mkdir($cacheDir, 0775, true);
        }

        $GLOBALS['template_cache_dir'] = $cacheDir;
    }

    /**
     * Include Main Template File
     */
    public function _includeTemplate(): void
    {
        if (empty($this->_scope)) {
            $this->_scope = static::DEFAULT_SCOPE;
        }

        $templateFilePath = sprintf(
            '%s/%s/index.phtml',
            static::TEMPLATE_DIR,
            $this->_scope
        );

        if (!file_exists($templateFilePath) || !is_file($templateFilePath)) {
            $errorMessage = sprintf(
                'Template "%s" Is Not Found',
                $this->_scope
            );

            throw new Exception($errorMessage);
        }

        include_once($templateFilePath);
    }
}
