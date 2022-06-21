<?php

namespace Sonder\Plugins;

use Exception;
use Sonder\Plugins\Theme\ThemePluginException;

final class ThemePlugin
{
    private const DEFAULT_PUBLIC_DIR_PATH = __DIR__ . '/../../../../public';

    /**
     * @var string
     */
    private string $_theme;

    /**
     * @var string
     */
    private string $_themeDirPath;

    /**
     * @var string
     */
    private string $_publicDirPath;

    /**
     * @param string|null $theme
     * @throws ThemePluginException
     * @throws Exception
     */
    final public function __construct(?string $theme = null)
    {
        if (empty($theme)) {
            throw new ThemePluginException(
                ThemePluginException::MESSAGE_PLUGIN_THEME_PATH_NOT_SET_ERROR,
                ThemePluginException::CODE_PLUGIN_THEME_PATH_NOT_SET_ERROR,
            );
        }

        $this->_theme = $theme;

        $templaterPlugin = new TemplaterPlugin($theme);

        $themeDirPath = $templaterPlugin->getThemePath($theme);

        if (!file_exists($themeDirPath) || !is_dir($themeDirPath)) {
            throw new ThemePluginException(
                ThemePluginException::MESSAGE_PLUGIN_THEME_NOT_FOUND_ERROR,
                ThemePluginException::CODE_PLUGIN_THEME_NOT_FOUND_ERROR,
            );
        }

        $this->_themeDirPath = $themeDirPath;

        $this->_publicDirPath = $this->_getPublicDirPath();

        if (
            !file_exists($this->_publicDirPath) ||
            !is_dir($this->_publicDirPath)
        ) {
            throw new ThemePluginException(
                ThemePluginException::MESSAGE_PLUGIN_PUBLIC_DIR_NOT_FOUND_ERROR,
                ThemePluginException::CODE_PLUGIN_PUBLIC_DIR_NOT_FOUND_ERROR,
            );
        }
    }

    /**
     * @throws ThemePluginException
     */
    final public function moveAssets(): void
    {
        $themeAssets = null;

        $metaValues = $this->_getMetaValues();

        if (
            array_key_exists('assets', $metaValues) &&
            is_array($metaValues['assets'])
        ) {
            $themeAssets = $metaValues['assets'];
        }

        foreach ((array)$themeAssets as $type => $files) {
            if (!empty($files) && is_array($files)) {
                $this->_moveAssetsByType($type, $files);
            }
        }
    }

    /**
     * @throws ThemePluginException
     */
    final public function compileLessFiles(): void
    {
        $lessAssets = null;

        $metaValues = $this->_getMetaValues();

        if (
            array_key_exists('assets', $metaValues) &&
            is_array($metaValues['assets']) &&
            array_key_exists('less', $metaValues['assets']) &&
            is_array($metaValues['assets']['less'])
        ) {
            $lessAssets = $metaValues['assets']['less'];
        }

        foreach ((array)$lessAssets as $file) {
            if (!empty($file)) {
                $this->_compileLessFile($file);
            }
        }
    }

    /**
     * @param string $file
     * @throws ThemePluginException
     */
    private function _compileLessFile(string $file): void
    {
        $file = explode('/', $file);
        $file = end($file);

        $lessFilePath = sprintf(
            '%s/less/%s/%s',
            $this->_getAssetsDirPath(),
            $this->_theme,
            $file
        );

        if (
            empty($cssFileName) ||
            $cssFileName == 'theme' ||
            $cssFileName == 'style' ||
            $cssFileName == 'app' ||
            $cssFileName == 'main'
        ) {
            $cssFileName = $this->_theme;
        }

        if ($cssFileName != $this->_theme) {
            $cssFileName = sprintf('%s_%s', $cssFileName, $this->_theme);
        }

        $cssFilePath = sprintf(
            '%s/css/%s/%s.min.css',
            $this->_getAssetsDirPath(),
            $this->_theme,
            $cssFileName
        );

        $cliDirPath = $this->_getCliDirPath();

        `bash $cliDirPath/less.sh -i $lessFilePath -o $cssFilePath`;
    }

    /**
     * @param string $type
     * @param array $files
     * @throws ThemePluginException
     */
    private function _moveAssetsByType(string $type, array $files): void
    {
        $publicAssetsDirPath = sprintf(
            '%s/%s/%s',
            $this->_getAssetsDirPath(),
            $type,
            $this->_theme
        );

        if (
            !file_exists($publicAssetsDirPath) ||
            !is_dir($publicAssetsDirPath)
        ) {
            mkdir($publicAssetsDirPath, 0755, true);
        }

        foreach ($files as $file) {
            $themeFilePath = sprintf(
                '%s/%s',
                $this->_themeDirPath,
                $file
            );

            $file = explode('/', $file);
            $file = end($file);

            if (!preg_match('/^min\.(.*?)$/su', $file)) {
                $file = str_replace('.min', '', $file);
            }

            $publicFilePath = sprintf(
                '%s/%s',
                $publicAssetsDirPath,
                $file
            );

            $this->_moveAssetFile($themeFilePath, $publicFilePath);
        }
    }

    /**
     * @param string $themeFilePath
     * @param string $publicFilePath
     * @throws ThemePluginException
     */
    private function _moveAssetFile(
        string $themeFilePath,
        string $publicFilePath
    ): void {
        if (!file_exists($themeFilePath) || !is_file($themeFilePath)) {
            $errorMessage = sprintf(
                ThemePluginException::MESSAGE_PLUGIN_ASSETS_FILE_MISSING_ERROR,
                $themeFilePath
            );

            throw new ThemePluginException(
                $errorMessage,
                ThemePluginException::CODE_PLUGIN_ASSETS_FILE_MISSING_ERROR
            );
        }

        if (file_exists($publicFilePath) && is_file($publicFilePath)) {
            unlink($publicFilePath);
        }

        copy($themeFilePath, $publicFilePath);
        chmod($publicFilePath, 0755);
    }

    /**
     * @return array
     * @throws ThemePluginException
     */
    private function _getMetaValues(): array
    {
        $metaFilePath = $this->_getMetaFilePath();

        $metaValues = file_get_contents($metaFilePath);

        return (array)json_decode((string)$metaValues, true);
    }

    /**
     * @return string
     * @throws ThemePluginException
     */
    private function _getMetaFilePath(): string
    {
        $metaFilePath = sprintf('%s/meta.json', $this->_themeDirPath);

        if (!file_exists($metaFilePath) || !is_file($metaFilePath)) {
            throw new ThemePluginException(
                ThemePluginException::MESSAGE_PLUGIN_META_FILE_NOT_FOUND_ERROR,
                ThemePluginException::CODE_PLUGIN_META_FILE_NOT_FOUND_ERROR
            );
        }

        return $metaFilePath;
    }

    /**
     * @return string
     */
    private function _getAssetsDirPath(): string
    {
        $assetsDirPath = sprintf('%s/assets', $this->_publicDirPath);

        if (!file_exists($assetsDirPath) && !is_dir($assetsDirPath)) {
            mkdir($assetsDirPath, 0755, true);
        }

        return $assetsDirPath;
    }

    /**
     * @return string
     */
    private function _getPublicDirPath(): string
    {
        if (defined('APP_PUBLIC_DIR_PATH')) {
            return APP_PUBLIC_DIR_PATH;
        }

        return ThemePlugin::DEFAULT_PUBLIC_DIR_PATH;
    }

    /**
     * @return string
     * @throws ThemePluginException
     */
    private function _getCliDirPath(): string
    {
        $cliDirPath = sprintf('%s/../cli', $this->_getPublicDirPath());
        $cliDirPath = realpath($cliDirPath);

        if (empty($cliDirPath)) {
            throw new ThemePluginException(
                ThemePluginException::MESSAGE_PLUGIN_CLI_DIR_MISSING_ERROR,
                ThemePluginException::CODE_PLUGIN_CLI_DIR_MISSING_ERROR
            );
        }

        return $cliDirPath;
    }
}
