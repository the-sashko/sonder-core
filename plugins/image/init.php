<?php
$imagePluginAutoload = function (string $dir, Closure $autoload): void {
    foreach (glob($dir . '/*') as $fileItem) {
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

require_once __DIR__ . '/exceptions/ImageException.php';

$imagePluginAutoload(__DIR__ . '/exceptions', $imagePluginAutoload);
$imagePluginAutoload(__DIR__ . '/interfaces', $imagePluginAutoload);
$imagePluginAutoload(__DIR__ . '/classes', $imagePluginAutoload);

require_once __DIR__ . '/ImagePlugin.php';
