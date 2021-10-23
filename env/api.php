<?php
if (!defined('APP_ENDPOINT')) {
    define('APP_ENDPOINT', 'api');
}

if (!defined('APP_RESPONSE_FORMAT')) {
    define('APP_RESPONSE_FORMAT', 'json');
}

if (!defined('APP_CACHE')) {
    define('APP_CACHE', true);
}

if (!defined('APP_CACHE_TTL')) {
    define('APP_CACHE_TTL', 60 * 30);
}
