<?php
ini_set('error_reporting', (string)E_ERROR);
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');

if (!defined('APP_ENDPOINT')) {
    define('APP_ENDPOINT', 'app');
}

if (!defined('APP_RESPONSE_FORMAT')) {
    define('APP_RESPONSE_FORMAT', 'html');
}

if (!defined('APP_CACHE')) {
    define('APP_CACHE', true);
}

if (!defined('APP_CACHE_TTL')) {
    define('APP_CACHE_TTL', 1800); // 30 min
}
