<?php
require_once __DIR__ . '/classes/exception.class.php';

if (!defined('APP_MODE')) {
    define('APP_MODE', 'default');
}

if (!defined('APP_AREA')) {
    define('APP_AREA', 'default');
}

if (!defined('APP_MULTI_LANGUAGE')) {
    define('APP_MULTI_LANGUAGE', false);
}

switch (APP_MODE) {
    case 'dev':
        define('OUTPUT_FORMAT', 'html');
        require_once __DIR__ . '/env/dev.php';
        require_once __DIR__ . '/app.php';
        (new App)->run();
        break;

    case 'prod':
        define('OUTPUT_FORMAT', 'html');
        require_once __DIR__ . '/env/prod.php';
        require_once __DIR__ . '/app.php';
        (new App)->run();
        break;

    case 'test':
        define('OUTPUT_FORMAT', 'text');
        require_once __DIR__ . '/env/cli.php';
        require_once __DIR__ . '/app.php';
        require_once __DIR__ . '/test.php';
        (new Test)->run();
        break;

    case 'api':
        define('OUTPUT_FORMAT', 'json');
        require_once __DIR__ . '/env/api.php';
        require_once __DIR__ . '/app.php';
        require_once __DIR__ . '/api.php';
        (new API)->run();
        break;

    case 'cli':
        define('OUTPUT_FORMAT', 'text');
        require_once __DIR__ . '/env/cli.php';
        require_once __DIR__ . '/app.php';
        require_once __DIR__ . '/cli.php';
        (new Cli)->run();
        break;

    default:
        define('OUTPUT_FORMAT', 'html');
        require_once __DIR__ . '/env/prod.php';
        require_once __DIR__ . '/app.php';
        (new App)->run();
        break;
}
