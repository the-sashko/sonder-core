<?php
ini_set('error_reporting', (string)E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

if (!defined('APP_ENDPOINT')) {
    define('APP_ENDPOINT', 'test');
}

if (!defined('APP_RESPONSE_FORMAT')) {
    define('APP_RESPONSE_FORMAT', 'text');
}
