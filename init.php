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
        require_once __DIR__.'/config/dev.php';
        require_once $routerPath;
        require_once __DIR__.'/app.php';
        (new App)->run();
        break;

    case 'prod':
        require_once __DIR__.'/config/prod.php';
        require_once $routerPath;
        require_once __DIR__.'/app.php';
        (new App)->run();
        break;

    case 'test':
        require_once __DIR__.'/config/test.php';
        require_once $routerPath;
        require_once __DIR__.'/app.php';
        require_once __DIR__.'/test.php';
        (new Test)->run();
        break;

    case 'api':
        require_once __DIR__.'/config/api.php';
        require_once $routerPath;
        require_once __DIR__.'/app.php';
        require_once __DIR__.'/api.php';
        (new API)->run();
        break;
    
    case 'deamon':
        require_once __DIR__.'/config/deamon.php';
        require_once $routerPath;
        require_once __DIR__.'/app.php';
        require_once __DIR__.'/deamon.php';
        (new Deamon)->run();
        break;

    case 'default':
        require_once __DIR__.'/config/prod.php';
        require_once $routerPath;
        require_once __DIR__.'/app.php';
        (new App)->run();
        break;
}
?>
