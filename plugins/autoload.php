<?php
class PluginsAutoload
{
    public function load(): void
    {
        array_map([$this, '_loadPlugin'], glob(__DIR__.'/*', GLOB_ONLYDIR));
    }

    private function _loadPlugin(?string $pluginDir = null): bool
    {
        if (empty($pluginDir)) {
            return false;
        }

        $pluginInitPath = "{$pluginDir}/init.php";

        if (!file_exists($pluginInitPath) || !is_file($pluginInitPath)) {
            return false;
        }

        require_once $pluginInitPath;

        return true;
    }
}

(new PluginsAutoload)->load();
