<?php
if (!defined('APP_MODE')) {
    define('APP_MODE', 'default');
}

$routerPath = __DIR__.'/examples/protected/routers/default.php';

if (file_exists(__DIR__.'/../routers/default.php')) {
    $routerPath = __DIR__.'/../routers/default.php';
}

switch (APP_MODE) {
    case 'dev':
        require_once __DIR__.'/env/dev.php';
        require_once $routerPath;
        require_once __DIR__.'/app.php';
        (new App)->run();
        break;

    case 'prod':
        require_once __DIR__.'/env/prod.php';
        require_once $routerPath;
        require_once __DIR__.'/app.php';
        (new App)->run();
        break;

    case 'test':
        require_once __DIR__.'/env/test.php';
        require_once $routerPath;
        require_once __DIR__.'/app.php';
        require_once __DIR__.'/test.php';
        (new Test)->run();
        break;

    case 'api':
        require_once __DIR__.'/env/api.php';
        require_once $routerPath;
        require_once __DIR__.'/app.php';
        require_once __DIR__.'/api.php';
        (new API)->run();
        break;
    
    case 'cli':
        require_once __DIR__.'/env/cli.php';
        require_once $routerPath;
        require_once __DIR__.'/app.php';
        require_once __DIR__.'/cli.php';
        (new CLI)->run();
        break;

    case 'default':
        require_once __DIR__.'/env/prod.php';
        require_once $routerPath;
        require_once __DIR__.'/app.php';
        (new App)->run();
        break;
}
