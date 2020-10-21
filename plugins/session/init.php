<?php
$sessionPluginAutoload = function(string $dir, Closure $autoload): void
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

require_once __DIR__.'/exceptions/SessionException.php';

$sessionPluginAutoload(__DIR__.'/interfaces', $sessionPluginAutoload);
$sessionPluginAutoload(__DIR__.'/classes', $sessionPluginAutoload);

require_once __DIR__.'/session.plugin.php';
