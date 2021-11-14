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

        if (!$this->_setValuesFromCache()) {
            $this->_setValues();
            $this->_saveValuesToCache();
        }
    }

    /**
     * @param string|null $configName
     * @return array
     * @throws Exception
     */
    final public function get(?string $configName = null): array
    {
        if (empty($configName)) {
            throw new Exception('Config Name Is Not Set');
        }

        if (!array_key_exists($configName, $this->_values)) {
            throw new Exception(
                sprintf('Config %s Is Not Exists', $configName)
            );
        }

        return (array)$this->_values[$configName];
    }

    /**
     * @param string|null $configsDirPath
     * @throws Exception
     */
    private function _setValues(?string $configsDirPath = null): void
    {
        $configPaths = [
            APP_PROTECTED_DIR_PATH . '/config'
        ];

        if (
            array_key_exists('config', APP_SOURCE_PATHS) &&
            is_array(APP_SOURCE_PATHS['config'])
        ) {
            $configPaths = APP_SOURCE_PATHS['config'];
        }

        if (empty($configsDirPath)) {
            $appConfigDirPaths = array_reverse($configPaths);

            foreach ($appConfigDirPaths as $appConfigDirPath) {
                $this->_setValues($appConfigDirPath);
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
     * @return string
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
    private function _setValuesFromCache(): bool
    {
        $values = $this->_cache->get('values');

        if (empty($values)) {
            return false;
        }

        $this->_values = $values;

        return true;
    }

    /**
     * @throws Exception
     */
    private function _saveValuesToCache(): void
    {
        $this->_cache->save(
            'values',
            $this->_values,
            ConfigObject::CACHE_TTL
        );
    }
}
