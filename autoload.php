<?php
namespace Sonder;

use Closure;
use Sonder\Core\AutoloadCore;

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

require_once (__DIR__ . '/core/config.object.class.php');
require_once (__DIR__ . '/core/core.object.class.php');

$autoload(__DIR__ . '/core/interfaces', $autoload);
$autoload(__DIR__ . '/core', $autoload);

spl_autoload_register(function ($name) {
    (new AutoloadCore)->load($name);
});
