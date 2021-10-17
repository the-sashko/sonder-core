<?php
$smsPluginAutoload = function(string $dir, Closure $autoload): void
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

$smsPluginAutoload(__DIR__.'/interfaces', $smsPluginAutoload);
$smsPluginAutoload(__DIR__.'/platforms', $smsPluginAutoload);

require_once __DIR__.'/SmsPlugin.php';
