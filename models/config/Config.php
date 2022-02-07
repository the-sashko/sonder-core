<?php

namespace Sonder\Models;

use Exception;
use Sonder\Core\CoreModel;
use Sonder\Core\Interfaces\IModel;
use Sonder\Core\ValuesObject;
use Sonder\Models\Config\ConfigValuesObject;
use Throwable;

final class Config extends CoreModel implements IModel
{
    const CONFIG_DIR_PATH_PATTERN = '%s/config';

    const PROHIBITED_CONFIGS = [
        'crypt',
        'database',
        'hooks',
        'locale',
        'share'
    ];

    /**
     * @var array
     */
    private array $_configs = [];

    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();

        $this->_setConfigs();
    }

    /**
     * @return void
     * @throws Exception
     */
    private function _setConfigs(): void
    {
        foreach ($this->_setConfigFilePaths() as $configFilePath) {
            $this->_setConfigFromFile($configFilePath);
        }
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
     * @return ConfigValuesObject|null
     */
    final public function getConfig(?string $name = null): ?ConfigValuesObject
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
     * @throws Exception
     */
    final public function updateConfig(string $name, array $inputValues): array
    {
        $errors = [];

        try {
            $configVO = $this->getConfig($name);

            if (empty($configVO)) {
                $errors[] = sprintf('Config "%s" is not exists', $name);
            }

            $values = $configVO->getValues();

            foreach ($inputValues as $name => $value) {
                $values[$name] = $value;
            }

            file_put_contents($configVO->getFilePath(), json_encode($values));
        } catch (Throwable $exp) {
            $errors[] = $exp->getMessage();
        }

        return $errors;
    }

    /**
     * @return array
     * @throws Exception
     */
    private function _setConfigFilePaths(): array
    {
        if (!defined('APP_PROTECTED_DIR_PATH')) {
            throw new Exception(
                'Const APP_PROTECTED_DIR_PATH is not defined'
            );
        }

        $configDirPath = sprintf(
            Config::CONFIG_DIR_PATH_PATTERN,
            APP_PROTECTED_DIR_PATH
        );

        return (array)glob(sprintf('%s/*.json', $configDirPath));
    }

    /**
     * @param string|null $configFilePath
     * @return void
     * @throws Exception
     */
    private function _setConfigFromFile(?string $configFilePath = null): void
    {
        if (empty($configFilePath) && !is_file($configFilePath)) {
            return;
        }

        /* @var $configVO ConfigValuesObject|null */
        $configVO = $this->_getConfigVOFromFile($configFilePath);

        if (empty($configVO)) {
            return;
        }

        $this->_configs[$configVO->getName()] = $configVO;
    }

    /**
     * @param string $configFilePath
     * @return ValuesObject|null
     * @throws Exception
     */
    private function _getConfigVOFromFile(string $configFilePath): ?ValuesObject
    {
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

        if (in_array($configFileName, Config::PROHIBITED_CONFIGS)) {
            return null;
        }

        return $this->getVO([
            'name' => $configFileName,
            'file_path' => $configFilePath,
            'values' => $configValues
        ]);
    }
}
