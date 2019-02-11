<?php
/**
 * Core Model Class
 */
class ModelCore extends CommonCore
{
	const MAIN_CONFIG_PATH = __DIR__.'/../../config/main.json';

	public $object     = null;
	public $configData = [];

    /**
     * summary
     */
	public function setObject(string $objectClassName = '') : void
	{
		if ($this->object === null) {
			$this->object = new $objectClassName();
			$this->object->initStore();
		}
	}

	/**
     * summary
     */
	public function setConfigData() : void
	{
		$configDataJSON = file_get_contents(self::MAIN_CONFIG_PATH);
		$this->configData = json_decode($configDataJSON, true);
	}

    /**
     * summary
     */
	public function getConfigValue(string $valueName = '') : string
	{
		if (isset($this->configData[$valueName])) {
			return (string) $this->configData[$valueName];
		}

		return NULL;
	}

	/**
     * summary
     */
	public function getConfigArrayValue(string $valueName = '') : array
	{
		if (isset($this->configData[$valueName])) {
			return (array) $this->configData[$valueName];
		}

		return [];
	}
}
?>