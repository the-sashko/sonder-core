<?php

namespace Sonder\Plugins;

use Exception;

final class TemplaterPlugin
{
    const PROTECTED_DIR_PATH = __DIR__ . '/../../..';

    /**
     * @var string|null
     */
    private ?string $_themePath = null;

    /**
     * @param string|null $theme
     *
     * @throws Exception
     */
    final public function __construct(?string $theme = null)
    {
        if (empty($theme)) {
            throw new Exception('Template Theme Is Not Set');
        }

        $themePath = sprintf('%s/phtml', $this->getThemePath($theme));

        $templateFilePath = sprintf('%s/main.phtml', $themePath);

        if (!file_exists($templateFilePath) || !is_file($templateFilePath)) {
            throw new Exception('Template Theme Has Bad Format');
        }

        $this->_themePath = $themePath;
    }

    /**
     * @param string $page
     * @param array|null $values
     * @param int|null $ttl
     *
     * @return string
     */
    final public function render(
        string $page,
        ?array $values = null,
        ?int   $ttl = null
    ): string
    {
        $GLOBALS['template'] = [
            'dir' => $this->_themePath,
            'values' => (array)$values,
            'ttl' => (int)$ttl,
            'cache_dir' => $this->_getCacheDirPath($page)
        ];

        foreach ($GLOBALS['template']['values'] as $valueName => $value) {
            $valueName = ucwords($valueName, '_');
            $valueName = lcfirst($valueName);
            $valueName = explode('_', $valueName);
            $valueName = implode('', $valueName);

            $$valueName = $value;
        }

        return $this->_renderPage($page);
    }

    /**
     * @param string $theme
     *
     * @return string
     *
     * @throws Exception
     */
    final public function getThemePath(string $theme): string
    {
        $themePath = null;

        $protectedDirPath = TemplaterPlugin::PROTECTED_DIR_PATH;

        if (defined('APP_PROTECTED_DIR_PATH')) {
            $protectedDirPath = APP_PROTECTED_DIR_PATH;
        }

        $themesPaths = [
            $protectedDirPath . '/themes'
        ];

        if (
            array_key_exists('themes', APP_SOURCE_PATHS) &&
            is_array(APP_SOURCE_PATHS['themes'])
        ) {
            $themesPaths = APP_SOURCE_PATHS['themes'];
        }

        foreach (array_reverse($themesPaths) as $path) {
            $templatePath = sprintf('%s/%s/phtml', $path, $theme);

            if (file_exists($templatePath) && is_dir($templatePath)) {
                $themePath = sprintf('%s/%s', $path, $theme);
            }
        }

        if (empty($themePath)) {
            throw new Exception(
                sprintf('Frontend Theme %s Is Not Exists', $theme)
            );
        }

        return $themePath;
    }

    /**
     * @param string $page
     *
     * @return string
     */
    private function _getCacheDirPath(string $page): string
    {
        $protectedDirPath = TemplaterPlugin::PROTECTED_DIR_PATH;

        if (defined('APP_PROTECTED_DIR_PATH')) {
            $protectedDirPath = APP_PROTECTED_DIR_PATH;
        }

        $cacheDirPath = sprintf(
            '%s/cache/templater/%s_%s/%s',
            $protectedDirPath,
            hash('md5', $_SERVER['REQUEST_URI']),
            hash('sha256', $_SERVER['REQUEST_URI']),
            $page
        );

        if (!file_exists($cacheDirPath) || !is_dir($cacheDirPath)) {
            mkdir($cacheDirPath, 0775, true);
        }

        return $cacheDirPath;
    }

    /**
     * @param string|null $templatePage
     *
     * @return string
     */
    private function _renderPage(?string $templatePage = null): string
    {
        ob_start();

        if (!empty($templatePage)) {
            include_once(sprintf('%s/main.phtml', $this->_themePath));
        }

        return (string)ob_get_clean();
    }
}
