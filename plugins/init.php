<?php
foreach (glob(__DIR__.'/*', GLOB_ONLYDIR) as $pluginDir) {
    if (file_exists("{$pluginDir}/init.php")) {
        require_once "{$pluginDir}/init.php";
    }
}
?>