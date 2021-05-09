<?php
$routerPluginAutoload = function(string $dir, Closure $autoload): void
{
    foreach (glob($dir.'/*') as $fileItem) {
        if ($fileItem == __FILE__) {
            continue;
        }

        if (is_dir($fileItem)) {
            $autoload($fileItem, $autoload);

            continue;
        }

        if (preg_match('/^(.*?)\.php$/', $fileItem)) {
            include_once $fileItem;
        }
    }
};

require_once __DIR__.'/exceptions/RouterException.php';

$routerPluginAutoload(__DIR__.'/exceptions', $routerPluginAutoload);
$routerPluginAutoload(__DIR__.'/interfaces', $routerPluginAutoload);
$routerPluginAutoload(__DIR__.'/classes', $routerPluginAutoload);

require_once __DIR__.'/router.plugin.php';
