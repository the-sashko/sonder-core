<?php
if (!defined('APP_MODE')) {
    define('APP_MODE', 'default');
}

switch (APP_MODE) {
    case 'dev':
        require_once __DIR__.'/config/dev.php';
        break;
    
    case 'prod':
        require_once __DIR__.'/config/prod.php';
        break;

    case 'test':
        require_once __DIR__.'/config/test.php';
        break;
}

if (file_exists(__DIR__.'/../router.php')) {
    require_once __DIR__.'/../router.php';
} else {
    require_once __DIR__.'/examples/router.php';
}

require_once __DIR__.'/app.php';
(new App)->run();

?>