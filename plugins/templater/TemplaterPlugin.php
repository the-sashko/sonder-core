<?php

namespace Sonder\Plugins;

use Exception;

final class TemplaterPlugin
{
    const DEFAULT_TEMPLATE = 'main';

    const DEFAULT_AREA = 'default';

    const PROTECTED_DIR_PATH = __DIR__ . '/../../..';

    private ?string $_area = null;

    private ?string $_templatePage = null;

    private ?string $_templateTheme = null;

    /**
     * @param string|null $templateTheme
     *
     * @throws Exception
     */
    final public function __construct(?string $templateTheme = null)
    {
        if (empty($templateTheme)) {
            throw new Exception('Template Theme Is Not Set');
        }

        $this->_templateTheme = $templateTheme;
    }

    /**
     * @param string|null $templatePage
     * @param array|null $dataParams
     * @param int $ttl
     *
     * @throws Exception
     */
    final public function render(
        ?string $templatePage = null,
        ?array  $dataParams = null,
        int     $ttl = 0
    ): void
    {
        if (empty($templatePage)) {
            $templatePage = TemplaterPlugin::DEFAULT_TEMPLATE;
        }

        if (empty($this->_area)) {
            $this->_area = TemplaterPlugin::DEFAULT_AREA;
        }

        $this->_templatePage = $templatePage;

        $GLOBALS['template_dir'] = $this->_getTemplateDirPath();
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
     * @param string|null $area
     */
    final public function setArea(?string $area = null): void
    {
        if (empty($area)) {
            $area = TemplaterPlugin::DEFAULT_AREA;
        }

        $this->_area = $area;
    }

    private function _setCacheDir(): void
    {
        if (empty($this->_area)) {
            $this->_area = TemplaterPlugin::DEFAULT_AREA;
        }

        $cacheSlug = sprintf(
            '%s_%s',
            hash('md5', $_SERVER['REQUEST_URI']),
            hash('sha256', $_SERVER['REQUEST_URI'])
        );

        $cacheDir = sprintf(
            '%s/%s/%s/%s',
            $this->_getCacheDirPath(),
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
     * @throws Exception
     */
    private function _includeTemplate(): void
    {
        if (empty($this->_area)) {
            $this->_area = TemplaterPlugin::DEFAULT_AREA;
        }

        $templateFilePath = sprintf(
            '%s/%s/index.phtml',
            $this->_getTemplateDirPath(),
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

    /**
     * @return string
     */
    private function _getTemplateDirPath(): string
    {
        $protectedDirPath = TemplaterPlugin::PROTECTED_DIR_PATH;

        if (defined(APP_PROTECTED_DIR_PATH)) {
            $protectedDirPath = APP_PROTECTED_DIR_PATH;
        }

        return sprintf(
            '%s/themes/%s/phtml',
            $protectedDirPath,
            $this->_templateTheme
        );
    }

    /**
     * @return string
     */
    private function _getCacheDirPath(): string
    {
        $protectedDirPath = TemplaterPlugin::PROTECTED_DIR_PATH;

        if (defined(APP_PROTECTED_DIR_PATH)) {
            $protectedDirPath = APP_PROTECTED_DIR_PATH;
        }

        return sprintf('%s/cache/templater', $protectedDirPath);
    }
}
