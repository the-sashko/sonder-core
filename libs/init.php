<?php
foreach (glob(__DIR__.'/*', GLOB_ONLYDIR) as $libDir) {
    if (file_exists("{$libDir}/init.php")) {
        require_once "{$libDir}/init.php";
    }
}
?>