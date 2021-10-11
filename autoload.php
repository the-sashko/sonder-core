<?php
namespace SonderCore;

use Closure;
use SonderCore\Core\AutoloadCore;

$autoload = function (string $directory, Closure $autoload) {
    foreach ((array)glob($directory . '/*') as $filePath) {
        if (is_dir($filePath)) {
            $autoload($filePath, $autoload);

            continue;
        }

        if (preg_match('/^(.*?)\.php$/su', $filePath)) {

            require_once $filePath;
        }
    }
};

$autoload(__DIR__ . '/core/interfaces', $autoload);
$autoload(__DIR__ . '/core', $autoload);

spl_autoload_register(function ($name) {
    (new AutoloadCore)->load($name);
});
