<?php

namespace Sonder\Core;

use Exception;

final class ConfigObject
{
    const CACHE_TTL = 60 * 30;

    /**
     * @var array
     */
    private array $_values;

    /**
     * @var CacheObject
     */
    private CacheObject $_cache;

    /**
     * @throws Exception
     */
    final public function __construct()
    {
        $this->_cache = new CacheObject('config');

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

        $configFilePathPattern = sprintf('%s/*.json', $configsDirPath);

        foreach ((array)glob($configFilePathPattern) as $configFilePath) {
            if (!is_file($configFilePath)) {
                continue;
            }

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
        $values = $this->_cache->get('values');

        if (empty($values)) {
            $this->_values = $values;

            return false;
        }

        return true;
    }

    /**
     * @throws Exception
     */
    private function _saveToCache(): void
    {
        $this->_cache->set(
            'values',
            $this->_values,
            ConfigObject::CACHE_TTL
        );
    }
}
