<?php
class PluginsAutoload
{
    public $loadAll     = true;
    public $configFile  = __DIR__.'/../../config/plugins.json';
    public $list        = [];

    public function init() : bool
    {
        $this->_initConfig();

        if ($this->loadAll) {
            $this->_loadAll();

            return false;
        }

        $this->_loadListed();

        return true;
    }

    private function _loadAll() : void
    {
        foreach (glob(__DIR__.'/*', GLOB_ONLYDIR) as $pluginDir) {
            $this->_loadPlugin($pluginDir);
        }
    }

    private function _loadListed() : void
    {
        foreach ($this->list as $pluginName) {
            $pluginName = (string) $pluginName;
            $pluginName = mb_convert_case(trim($pluginName), MB_CASE_LOWER);

            $this->_loadPlugin(__DIR__.'/'.$pluginName);
        }
    }

    private function _loadPlugin(string $pluginDir = '') : void
    {
        if (
            file_exists("{$pluginDir}/init.php") &&
            is_file("{$pluginDir}/init.php")
        ) {
            require_once "{$pluginDir}/init.php";
        }
    }

    private function _initConfig(): bool
    {
        if (!file_exists($this->configFile) || !is_file($this->configFile)) {
            return false;
        }

        $config = file_get_contents($this->configFile);
        $config = json_decode($config, true);

        if (!array_key_exists('mode', $config) || $config['mode'] === 'all') {
            return false;
        }

        $this->loadAll = false;

        if (!array_key_exists('list', $config) || is_array($config['list'])) {
            return false;
        }

        $this->list = $config['list'];

        return true;
    }
}

(new PluginsAutoload)->init();
