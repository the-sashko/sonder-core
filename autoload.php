<?php

namespace Sonder;

require_once __DIR__ . '/core/utils/load.directory.function.php';

use Sonder\Core\AutoloadCore;
use function Sonder\Core\Utils\loadDirectory;

loadDirectory(__DIR__ . '/core/interfaces');

loadDirectory(__DIR__ . '/interfaces');

loadDirectory(__DIR__ . '/enums');

require_once(__DIR__ . '/core/values.object.class.php');
require_once(__DIR__ . '/core/cache.object.class.php');
require_once(__DIR__ . '/core/config.object.class.php');
require_once(__DIR__ . '/core/core.object.class.php');

loadDirectory(__DIR__ . '/core');

require_once(__DIR__ . '/exceptions/AppException.php');

loadDirectory(__DIR__ . '/exceptions');

$enumsPaths = [
    APP_PROTECTED_DIR_PATH . '/enums'
];

if (
    array_key_exists('enums', APP_SOURCE_PATHS) &&
    is_array(APP_SOURCE_PATHS['enums'])
) {
    $enumsPaths = APP_SOURCE_PATHS['enums'];
}

foreach ($enumsPaths as $enumsPath) {
    if (file_exists($enumsPath) && is_dir($enumsPath)) {
        loadDirectory($enumsPath);
    }
}

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
    if (file_exists($hooksPath) && is_dir($hooksPath)) {
        loadDirectory($hooksPath);
    }
}

spl_autoload_register(function ($name) {
    (new AutoloadCore)->load($name);
});
