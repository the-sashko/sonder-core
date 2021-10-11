<?php
ini_set('error_reporting', (string)E_ERROR);
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');

if (!defined('APP_ENDPOINT')) {
    define('APP_ENDPOINT', 'app');
}

if (!defined('APP_RESPONSE_TYPE')) {
    define('APP_RESPONSE_TYPE', 'html');
}
