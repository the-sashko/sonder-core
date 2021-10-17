<?php
$sharePluginAutoload = function (string $dir, Closure $autoload): void {
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

require_once __DIR__ . '/vendor/twitter/src/codebird.php';

require_once __DIR__ . '/exceptions/ShareException.php';

$sharePluginAutoload(__DIR__ . '/exceptions', $sharePluginAutoload);
$sharePluginAutoload(__DIR__ . '/interfaces', $sharePluginAutoload);

require_once __DIR__ . '/AbstractPlatform.php';

$sharePluginAutoload(__DIR__ . '/platforms', $sharePluginAutoload);

require_once __DIR__ . '/SharePlugin.php';
