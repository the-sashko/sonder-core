<?php

namespace Sonder\Plugins;

use Exception;
use Sonder\Plugins\Theme\ThemePluginException;

final class ThemePlugin
{
    const DEFAULT_PUBLIC_DIR_PATH = __DIR__ . '/../../../../public';

    const ASSETS_TYPES = [
        'js',
        'css',
        'less',
        'img',
        'font'
    ];

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
        foreach (ThemePlugin::ASSETS_TYPES as $type) {
            $this->_moveAssetsByType($type);
        }
    }

    /**
     * @param string $type
     * @return bool
     * @throws ThemePluginException
     */
    private function _moveAssetsByType(string $type): bool
    {
        $metaValues = $this->_getMetaValues();

        if (
            !array_key_exists($type, $metaValues) ||
            empty($metaValues[$type]) ||
            !is_array($metaValues[$type])
        ) {
            return false;
        }

        $publicAssetsDirPath = sprintf(
            '%s/%s',
            $this->_getAssetsDirPath(),
            $type
        );

        if (
            !file_exists($publicAssetsDirPath) ||
            !is_dir($publicAssetsDirPath)
        ) {
            mkdir($publicAssetsDirPath, 0755, true);
        }

        foreach ($metaValues[$type] as $file) {
            $themeFilePath = sprintf(
                '%s/%s/%s',
                $this->_themeDirPath,
                $type,
                $file
            );

            $publicFilePath = sprintf(
                '%s/%s',
                $publicAssetsDirPath,
                $file
            );

            $this->_moveAssetFile($themeFilePath, $publicFilePath);
        }

        return true;
    }

    /**
     * @param string $themeFilePath
     * @param string $publicFilePath
     * @throws ThemePluginException
     */
    private function _moveAssetFile(
        string $themeFilePath,
        string $publicFilePath
    ): void
    {
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
}
