<?php

namespace Sonder\Core;

use Exception;

final class ConfigObject
{
    const CACHE_DIR_PATH = __DIR__ . '/../../cache/config/';

    const CACHE_TTL = 60 * 30;

    /**
     * @var array
     */
    private array $_values;

    /**
     * @throws Exception
     */
    final public function __construct()
    {
        if (!$this->_setConfigsFromCache()) {
            $this->_setConfigs();
            $this->_saveToCache();
        }
    }

    /**
     * @param string|null $configName
     *
     * @return array
     *
     * @throws Exception
     */
    final public function get(?string $configName = null): array
    {
        if (empty($configName)) {
            throw new Exception('Config Name Is Not Set');
        }

        if (!array_key_exists($configName, $this->_values)) {
            throw new Exception('Config %s Is Not Exists', $configName);
        }

        return (array)$this->_values[$configName];
    }

    /**
     * @return string
     */
    private function _getCacheDirPath(): string
    {
        if (!defined('APP_PROTECTED_DIR_PATH')) {
            return ConfigObject::CACHE_DIR_PATH;
        }

        return sprintf(
            '%s/cache/config',
            APP_PROTECTED_DIR_PATH
        );
    }

    /**
     * @return string
     */
    private function _getCacheFilePath(): string
    {
        return sprintf('%s/values.json', $this->_getCacheDirPath());
    }

    /**
     * @param string|null $configsDirPath
     *
     * @throws Exception
     */
    private function _setConfigs(?string $configsDirPath = null): void
    {
        if (empty($configsDirPath)) {
            $appConfigDirPaths = array_reverse(APP_SOURCE_PATHS['config']);

            foreach ($appConfigDirPaths as $appConfigDirPath) {
                $this->_setConfigs($appConfigDirPath);
            }
        }

        $configFilePaths = (array)glob($configsDirPath . '/*.json');

        foreach ($configFilePaths as $configFilePath) {
            $configName = $this->_getConfigNameFormFilePath($configFilePath);

            $configValues = file_get_contents($configFilePath);
            $configValues = (array)json_decode($configValues, true);

            $this->_values[$configName] = $configValues;
        }
    }

    /**
     * @param string $configFilePath
     *
     * @return string
     *
     * @throws Exception
     */
    private function _getConfigNameFormFilePath(string $configFilePath): string
    {
        $configFileName = explode('/', $configFilePath);
        $configFileName = end($configFileName);

        $configName = preg_replace(
            '/^(.*?)\.json$/su',
            '$1',
            $configFileName
        );

        $configName = mb_convert_case($configName, MB_CASE_LOWER);

        $configName = preg_replace(
            '/[^a-z]/su',
            '_',
            $configName
        );

        $configName = preg_replace(
            '/([_]+)/su',
            '_',
            $configName
        );

        $configName = preg_replace(
            '/((^_)|(_$))/su',
            '',
            $configName
        );

        if (empty($configName)) {
            throw new Exception(sprintf(
                'Config File %s Has Bad Name',
                $configFileName
            ));
        }

        return $configName;
    }

    /**
     * @return bool
     */
    private function _setConfigsFromCache(): bool
    {
        $cacheFilePath = $this->_getCacheFilePath();

        if (!file_exists($cacheFilePath) || !is_file($cacheFilePath)) {
            return false;
        }

        $cacheValues = (string)file_get_contents($cacheFilePath);
        $cacheValues = (array)json_decode($cacheValues, true);

        if (!$this->_validateCacheValues($cacheValues)) {
            unlink($cacheFilePath);

            return false;
        }

        $this->_values = $cacheValues['values'];

        return true;
    }

    /**
     * @param array $cacheValues
     *
     * @return bool
     */
    private function _validateCacheValues(array $cacheValues): bool
    {
        if (
            !array_key_exists('timestamp', $cacheValues) ||
            !array_key_exists('values', $cacheValues) ||
            !is_array($cacheValues['values']) ||
            $cacheValues['timestamp'] < time()
        ) {
            return false;
        }

        $this->_values = $cacheValues['values'];

        return true;
    }

    private function _saveToCache(): void
    {
        $cacheDirPath = $this->_getCacheDirPath();
        $cacheFilePath = $this->_getCacheFilePath();

        if (!file_exists($cacheDirPath) || !is_dir($cacheDirPath)) {
            mkdir($cacheDirPath, 0755, true);
        }

        if (file_exists($cacheFilePath) && is_file($cacheFilePath)) {
            unlink($cacheFilePath);
        }

        $cacheValues = [
            'timestamp' => time() + ConfigObject::CACHE_TTL,
            'values' => $this->_values
        ];

        $cacheValues = json_encode($cacheValues);

        file_put_contents($cacheFilePath, $cacheValues);
    }
}
