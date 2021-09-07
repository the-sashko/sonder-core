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
     * @var string Default Area
     */
    const DEFAULT_AREA = 'default';

    /**
     * @var string Templates Directory Path
     */
    const TEMPLATE_DIR = __DIR__ . '/../../../res/tpl';

    /**
     * @var string Cache Directory Path
     */
    const CACHE_DIR_PATH = __DIR__ . '/../../../res/cache/tpl';

    /**
     * @var string|null Template Area
     */
    private ?string $_area = null;

    /**
     * @var string|null Template Page File Name
     */
    private ?string $_templatePage = null;

    /**
     * Generate And Display HTML Page From Template File
     *
     * @param string|null $templatePage Template Page File Name
     * @param array|null $dataParams Array Of Values For Using In Template
     *                                  Page
     * @param int $ttl Time To Live Template Cache
     *
     * @throws Exception
     */
    public function render(
        ?string $templatePage = null,
        ?array  $dataParams = null,
        int     $ttl = 0
    ): void
    {
        if (empty($templatePage)) {
            $templatePage = static::DEFAULT_TEMPLATE;
        }

        if (empty($this->_area)) {
            $this->_area = static::DEFAULT_AREA;
        }

        $this->_templatePage = $templatePage;

        $GLOBALS['template_dir'] = static::TEMPLATE_DIR;
        $GLOBALS['template_params'] = (array)$dataParams;
        $GLOBALS['template_area'] = $this->_area;
        $GLOBALS['template_ttl'] = $ttl;

        if ($ttl > 0) {
            $this->_setCacheDir();
        }

        foreach ($GLOBALS['template_params'] as $param => $value) {
            $$param = $value;
        }

        $this->_includeTemplate();
    }

    /**
     * Set Template Area
     */
    public function setArea(?string $area = null): void
    {
        if (empty($area)) {
            $area = static::DEFAULT_AREA;
        }

        $this->_area = $area;
    }

    /**
     * Generate And Display HTML Page From Template File
     */
    public function _setCacheDir(): void
    {
        if (empty($this->_area)) {
            $this->_area = static::DEFAULT_AREA;
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
            $this->_area,
            $this->_templatePage
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
        if (empty($this->_area)) {
            $this->_area = static::DEFAULT_AREA;
        }

        $templateFilePath = sprintf(
            '%s/%s/index.phtml',
            static::TEMPLATE_DIR,
            $this->_area
        );

        if (!file_exists($templateFilePath) || !is_file($templateFilePath)) {
            $errorMessage = sprintf(
                'Template "%s" Is Not Found',
                $this->_area
            );

            throw new Exception($errorMessage);
        }

        $templatePage = $this->_templatePage;

        include_once($templateFilePath);
    }
}
