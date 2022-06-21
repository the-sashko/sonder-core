<?php

foreach (glob(__DIR__ . '/*', GLOB_ONLYDIR) as $pluginDirPath) {
    $iniFilePath = sprintf('%s/init.php', $pluginDirPath);

    if (file_exists($iniFilePath) && is_file($iniFilePath)) {
        require_once $iniFilePath;
    }
}
