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

require_once(__DIR__ . '/core/values.object.class.php');
require_once(__DIR__ . '/core/cache.object.class.php');
require_once(__DIR__ . '/core/config.object.class.php');
require_once(__DIR__ . '/core/core.object.class.php');

$autoload(__DIR__ . '/core/interfaces', $autoload);
$autoload(__DIR__ . '/core', $autoload);

require_once(__DIR__ . '/exceptions/AppException.php');

$autoload(__DIR__ . '/exceptions', $autoload);

$hooksPaths = [
    APP_PROTECTED_DIR_PATH . '/hooks'
];

if (
    array_key_exists('hooks', APP_SOURCE_PATHS) &&
    is_array(APP_SOURCE_PATHS['hooks'])
) {
    $hooksPaths = APP_SOURCE_PATHS['hooks'];
}

foreach ($hooksPaths as $hooksPath) {
    if (file_exists($hooksPath) && $hooksPath) {
        $autoload($hooksPath, $autoload);
    }
}

spl_autoload_register(function ($name) {
    (new AutoloadCore)->load($name);
});
