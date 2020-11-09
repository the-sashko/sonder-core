<?php
require_once __DIR__.'/classes/exception.class.php';

if (!defined('APP_MODE')) {
    define('APP_MODE', 'default');
}

$routerPath = null;

if (defined('APP_ROUTER')) {
    $routerPath = __DIR__.'/../routers/'.APP_ROUTER.'.php';
}

if (
    !empty($routerPath) &&
    (
        !file_exists($routerPath) ||
        !is_file($routerPath)
    )
) {
    $errorMessage = '%s. Router: %s';

    $errorMessage = sprintf(
        $errorMessage,
        CoreException::MESSAGE_CORE_ROUTER_FILE_NOT_FOUND,
        APP_ROUTER
    );

    throw new CoreException(
        $errorMessage,
        CoreException::CODE_CORE_ROUTER_FILE_NOT_FOUND
    );
}

if (!empty($routerPath)) {
    require_once $routerPath;
}

switch (APP_MODE) {
    case 'dev':
        define('OUTPUT_FORMAT', 'html');
        require_once __DIR__.'/env/dev.php';
        require_once __DIR__.'/app.php';
        (new App)->run();
        break;

    case 'prod':
        define('OUTPUT_FORMAT', 'html');
        require_once __DIR__.'/env/prod.php';
        require_once __DIR__.'/app.php';
        (new App)->run();
        break;

    case 'test':
        define('OUTPUT_FORMAT', 'text');
        require_once __DIR__.'/env/cli.php';
        require_once __DIR__.'/app.php';
        require_once __DIR__.'/test.php';
        (new Test)->run();
        break;

    case 'api':
        define('OUTPUT_FORMAT', 'json');
        require_once __DIR__.'/env/api.php';
        require_once __DIR__.'/app.php';
        require_once __DIR__.'/api.php';
        (new API)->run();
        break;
    
    case 'cli':
        define('OUTPUT_FORMAT', 'text');
        require_once __DIR__.'/env/cli.php';
        require_once __DIR__.'/app.php';
        require_once __DIR__.'/cli.php';
        (new Cli)->run();
        break;

    case 'default':
        define('OUTPUT_FORMAT', 'html');
        require_once __DIR__.'/env/prod.php';
        require_once __DIR__.'/app.php';
        (new App)->run();
        break;
}
