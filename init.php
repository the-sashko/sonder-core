<?php
if (!defined('APP_MODE')) {
    define('APP_MODE', 'default');
}

if (!defined('APP_ROUTER')) {
    define('APP_ROUTER', 'default');
}

$routerPath = __DIR__.'/../routers/'.APP_ROUTER.'.php';

if (!file_exists($routerPath)) {
    throw new Exception('Router File Not Found');
}

switch (APP_MODE) {
    case 'dev':
        require_once __DIR__.'/env/dev.php';
        require_once $routerPath;
        require_once __DIR__.'/exceptions/app.exception.php';
        require_once __DIR__.'/app.php';
        (new App)->run();
        break;

    case 'prod':
        require_once __DIR__.'/env/prod.php';
        require_once $routerPath;
        require_once __DIR__.'/exceptions/app.exception.php';
        require_once __DIR__.'/app.php';
        (new App)->run();
        break;

    case 'test':
        require_once __DIR__.'/env/test.php';
        require_once $routerPath;
        require_once __DIR__.'/exceptions/app.exception.php';
        require_once __DIR__.'/exceptions/test.exception.php';
        require_once __DIR__.'/app.php';
        require_once __DIR__.'/test.php';
        (new Test)->run();
        break;

    case 'api':
        require_once __DIR__.'/env/api.php';
        require_once $routerPath;
        require_once __DIR__.'/exceptions/app.exception.php';
        require_once __DIR__.'/exceptions/api.exception.php';
        require_once __DIR__.'/app.php';
        require_once __DIR__.'/api.php';
        (new API)->run();
        break;
    
    case 'cli':
        require_once __DIR__.'/env/cli.php';
        require_once $routerPath;
        require_once __DIR__.'/exceptions/app.exception.php';
        require_once __DIR__.'/exceptions/cli.exception.php';
        require_once __DIR__.'/app.php';
        require_once __DIR__.'/cli.php';
        (new CLI)->run();
        break;

    case 'default':
        require_once __DIR__.'/env/prod.php';
        require_once $routerPath;
        require_once __DIR__.'/exceptions/app.exception.php';
        require_once __DIR__.'/app.php';
        (new App)->run();
        break;
}
