<?php
$mailPluginAutoload = function (string $dir, Closure $autoload): void {
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

$mailPluginAutoload(__DIR__ . '/interfaces', $mailPluginAutoload);

require_once __DIR__ . '/providers/AbstractMailProvider.php';

$mailPluginAutoload(__DIR__ . '/providers', $mailPluginAutoload);

require_once __DIR__ . '/MailPlugin.php';
