<?php
$rssPluginAutoload = function (string $dir, Closure $autoload): void {
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

require_once __DIR__ . '/exceptions/RssException.php';

$rssPluginAutoload(__DIR__ . '/exceptions', $rssPluginAutoload);
$rssPluginAutoload(__DIR__ . '/interfaces', $rssPluginAutoload);
$rssPluginAutoload(__DIR__ . '/classes', $rssPluginAutoload);

require_once __DIR__ . '/RssPlugin.php';
