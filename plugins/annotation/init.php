<?php
$annotationPluginAutoload = function(string $dir, Closure $autoload): void
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

require_once __DIR__.'/exceptions/AnnotationException.php';

$annotationPluginAutoload(__DIR__.'/exceptions', $annotationPluginAutoload);
$annotationPluginAutoload(__DIR__.'/interfaces', $annotationPluginAutoload);
$annotationPluginAutoload(__DIR__.'/classes', $annotationPluginAutoload);

require_once __DIR__.'/annotation.plugin.php';
