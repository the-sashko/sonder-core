<?php
$databasePluginAutoload = function(string $dir, Closure $autoload): void
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

require_once __DIR__.'/exceptions/DatabaseException.php';

$databasePluginAutoload(__DIR__.'/exceptions', $databasePluginAutoload);
$databasePluginAutoload(__DIR__.'/interfaces', $databasePluginAutoload);
$databasePluginAutoload(__DIR__.'/classes', $databasePluginAutoload);

require_once __DIR__.'/database.plugin.php';
