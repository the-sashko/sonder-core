<?php

namespace Sonder\Models;

use Sonder\Core\CoreModel;
use Sonder\Exceptions\ConfigException;
use Sonder\Exceptions\ModelException;
use Sonder\Interfaces\IModel;
use Sonder\Model\Config\Exceptions\ConfigException as ConfigExceptionAlias;
use Sonder\Model\Config\Exceptions\ConfigModelException;
use Sonder\Models\Config\Enums\ConfigProhibitedEnum;
use Sonder\Models\Config\Interfaces\IConfigModel;
use Sonder\Models\Config\Interfaces\IConfigValuesObject;
use Throwable;

/**
 * @property null $api
 * @property null $store
 */
#[IModel]
#[IConfigModel]
final class ConfigModel extends CoreModel implements IConfigModel
{
    private const CONFIG_DIR_PATH_PATTERN = '%s/config';

    /**
     * @var array
     */
    private array $_configs = [];

    /**
     * @throws ConfigModelException
     * @throws ModelException
     * @throws ConfigException
     */
    public function __construct()
    {
        parent::__construct();

        $this->_setConfigs();
    }

    /**
     * @return array
     */
    final public function getConfigs(): array
    {
        return $this->_configs;
    }

    /**
     * @param string|null $name
     * @return IConfigValuesObject|null
     */
    final public function getConfig(?string $name = null): ?IConfigValuesObject
    {
        if (empty($name)) {
            return null;
        }

        if (!array_key_exists($name, $this->_configs)) {
            return null;
        }

        return $this->_configs[$name];
    }

    /**
     * @param string $name
     * @param array $inputValues
     * @return array
     */
    final public function updateConfig(string $name, array $inputValues): array
    {
        $errors = [];

        try {
            $configVO = $this->getConfig($name);

            if (empty($configVO)) {
                $errors[] = sprintf('Config "%s" Not Exists', $name);
            }

            $values = $configVO->getValues();

            foreach ($inputValues as $name => $value) {
                $values[$name] = $value;
            }

            file_put_contents($configVO->getFilePath(), json_encode($values));
        } catch (Throwable $thr) {
            $errors[] = $thr->getMessage();
        }

        return $errors;
    }

    /**
     * @return void
     * @throws ConfigModelException
     * @throws ModelException
     */
    private function _setConfigs(): void
    {
        foreach ($this->_setConfigFilePaths() as $configFilePath) {
            $this->_setConfigFromFile($configFilePath);
        }
    }

    /**
     * @return array
     * @throws ConfigModelException
     */
    private function _setConfigFilePaths(): array
    {
        if (!defined('APP_PROTECTED_DIR_PATH')) {
            $errorMessage = sprintf(
                ConfigModelException::MESSAGE_MODEL_METHOD_CONST_IS_NOT_DEFINED,
                'APP_PROTECTED_DIR_PATH'
            );

            throw new ConfigModelException(
                $errorMessage,
                ConfigExceptionAlias::CODE_MODEL_METHOD_CONST_IS_NOT_DEFINED
            );
        }

        $configDirPath = sprintf(
            ConfigModel::CONFIG_DIR_PATH_PATTERN,
            APP_PROTECTED_DIR_PATH
        );

        return (array)glob(sprintf('%s/*.json', $configDirPath));
    }

    /**
     * @param string|null $configFilePath
     * @return void
     * @throws ModelException
     */
    private function _setConfigFromFile(?string $configFilePath = null): void
    {
        if (empty($configFilePath) && !is_file($configFilePath)) {
            return;
        }

        /* @var $configVO IConfigValuesObject|null */
        $configVO = $this->_getConfigVOFromFile($configFilePath);

        if (empty($configVO)) {
            return;
        }

        $this->_configs[$configVO->getName()] = $configVO;
    }

    /**
     * @param string $configFilePath
     * @return IConfigValuesObject|null
     * @throws ModelException
     */
    private function _getConfigVOFromFile(
        string $configFilePath
    ): ?IConfigValuesObject {
        $configValues = file_get_contents($configFilePath);
        $configValues = (array)json_decode($configValues, true);

        if (empty($configValues)) {
            return null;
        }

        $configFileName = explode('/', $configFilePath);
        $configFileName = end($configFileName);

        $configFileName = preg_replace(
            '/^(.*?)\.json$/su',
            '$1',
            (string)$configFileName
        );

        $configFileName = mb_convert_case($configFileName, MB_CASE_LOWER);

        if (ConfigProhibitedEnum::tryFrom($configFileName)) {
            return null;
        }

        /* @var $configVO IConfigValuesObject */
        $configVO = $this->getVO([
            'name' => $configFileName,
            'file_path' => $configFilePath,
            'values' => $configValues
        ]);

        return $configVO;
    }
}
