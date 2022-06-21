<?php

namespace Sonder\Core;

use Sonder\Enums\CacheTypesEnum;
use Sonder\Enums\ConfigNamesEnum;
use Sonder\Exceptions\AppException;
use Sonder\Exceptions\CacheException;
use Sonder\Exceptions\ConfigException;
use Sonder\Interfaces\ICacheObject;
use Sonder\Interfaces\IConfigObject;

#[IConfigObject]
final class ConfigObject implements IConfigObject
{
    private const CACHE_VALUE_NAME = 'values';

    private const CACHE_TTL = 1800; // 30 min

    private array $_values;

    #[ICacheObject]
    private CacheObject $_cache;

    /**
     * @throws CacheException
     * @throws ConfigException
     */
    final public function __construct()
    {
        $this->_cache = new CacheObject(CacheTypesEnum::CONFIG);

        if (!$this->_setValuesFromCache()) {
            $this->_setValues();
            $this->_saveValuesToCache();
        }
    }

    /**
     * @param string|ConfigNamesEnum $configName
     * @return array
     * @throws ConfigException
     */
    final public function get(string|ConfigNamesEnum $configName): array
    {
        if (!is_string($configName)) {
            $configName = $configName->value;
        }

        if (array_key_exists($configName, $this->_values)) {
            return (array)$this->_values[$configName];
        }

        if (empty(ConfigNamesEnum::tryFrom($configName))) {
            $errorMessage = sprintf(
                ConfigException::MESSAGE_CONFIG_INVALID_CONFIG_NAME,
                $configName
            );

            throw new ConfigException(
                $errorMessage,
                AppException::CODE_CONFIG_INVALID_CONFIG_NAME
            );
        }

        $errorMessage = sprintf(
            ConfigException::MESSAGE_CONFIG_NOT_EXISTS,
            $configName
        );

        throw new ConfigException(
            $errorMessage,
            AppException::CODE_CONFIG_NOT_EXISTS
        );
    }

    /**
     * @param string|ConfigNamesEnum $configName
     * @param string $valueName
     * @return string
     * @throws ConfigException
     */
    final public function getValue(
        string|ConfigNamesEnum $configName,
        string $valueName
    ): string {
        if (!is_string($configName)) {
            $configName = $configName->value;
        }

        if (empty($valueName)) {
            throw new ConfigException(
                ConfigException::MESSAGE_CONFIG_VALUE_NAME_IS_NOT_SET,
                AppException::CODE_CONFIG_VALUE_NAME_IS_NOT_SET
            );
        }

        $configValues = $this->get($configName);

        if (empty($configValues)) {
            $errorMessage = sprintf(
                ConfigException::MESSAGE_CONFIG_CONFIG_IS_EMPTY,
                $configName
            );

            throw new ConfigException(
                $errorMessage,
                AppException::CODE_CONFIG_CONFIG_IS_EMPTY
            );
        }

        if (array_key_exists($valueName, $configValues)) {
            return (string)$configValues[$valueName];
        }

        $errorMessage = sprintf(
            ConfigException::MESSAGE_CONFIG_CONFIG_HAS_NOT_VALUE,
            $configName,
            $valueName
        );

        throw new ConfigException(
            $errorMessage,
            AppException::CODE_CONFIG_CONFIG_HAS_NOT_VALUE
        );
    }

    /**
     * @param string|ConfigNamesEnum $configName
     * @param string $valueName
     * @return bool
     * @throws ConfigException
     */
    final public function hasValue(
        string|ConfigNamesEnum $configName,
        string $valueName
    ): bool {
        if (!is_string($configName)) {
            $configName = $configName->value;
        }

        if (empty($valueName)) {
            return false;
        }

        $configValues = $this->get($configName);

        if (empty($configValues)) {
            return false;
        }

        return array_key_exists($valueName, $configValues);
    }

    /**
     * @param string|null $configsDirPath
     * @return void
     * @throws ConfigException
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
     * @throws ConfigException
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
            '/[^a-z]/u',
            '_',
            $configName
        );

        $configName = preg_replace(
            '/(_+)/u',
            '_',
            $configName
        );

        $configName = preg_replace(
            '/((^_)|(_$))/u',
            '',
            $configName
        );

        if (!empty($configName)) {
            return $configName;
        }

        $errorMessage = sprintf(
            ConfigException::MESSAGE_CONFIG_CONFIG_FILE_HAS_BAD_FORMAT,
            $configFileName
        );

        throw new ConfigException(
            $errorMessage,
            AppException::CODE_CONFIG_CONFIG_FILE_HAS_BAD_FORMAT
        );
    }

    /**
     * @return bool
     */
    private function _setValuesFromCache(): bool
    {
        $values = $this->_cache->get(ConfigObject::CACHE_VALUE_NAME);

        if (empty($values)) {
            return false;
        }

        $this->_values = $values;

        return true;
    }

    /**
     * @return void
     * @throws CacheException
     */
    private function _saveValuesToCache(): void
    {
        $this->_cache->save(
            ConfigObject::CACHE_VALUE_NAME,
            $this->_values,
            ConfigObject::CACHE_TTL
        );
    }
}
