<?php

namespace Sonder\Plugins\Rss;

if (function_exists('\Sonder\Core\Utils\loadDirectory')) {
    $autoload = \Sonder\Core\Utils\loadDirectory(...);
} else {
    /**
     * @param string $directory
     * @return void
     */
    $autoload = function (string $directory) use (&$autoload): void {
        foreach (glob($directory . '/*') as $fileItem) {
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
}

require_once __DIR__ . '/exceptions/RssException.php';

$autoload(__DIR__ . '/exceptions');
$autoload(__DIR__ . '/interfaces');
$autoload(__DIR__ . '/classes');

require_once __DIR__ . '/RssPlugin.php';
