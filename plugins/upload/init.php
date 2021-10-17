<?php
$uploadPluginAutoload = function(string $dir, Closure $autoload): void
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

require_once __DIR__.'/exceptions/UploadException.php';

$uploadPluginAutoload(__DIR__.'/exceptions', $uploadPluginAutoload);
$uploadPluginAutoload(__DIR__.'/interfaces', $uploadPluginAutoload);
$uploadPluginAutoload(__DIR__.'/classes', $uploadPluginAutoload);

require_once __DIR__.'/UploadPlugin.php';
