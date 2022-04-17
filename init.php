<?php
try {
    if (!defined('APP_FRAMEWORK_DIR_PATH')) {
        define('APP_FRAMEWORK_DIR_PATH', realpath(__DIR__));
    }

    if (!defined('APP_PROTECTED_DIR_PATH')) {
        define(
            'APP_PROTECTED_DIR_PATH',
            realpath(APP_FRAMEWORK_DIR_PATH . '/../')
        );
    }

    if (!defined('APP_PUBLIC_DIR_PATH')) {
        define(
            'APP_PUBLIC_DIR_PATH',
            realpath(APP_PROTECTED_DIR_PATH . '/../public')
        );
    }

    if (!defined('APP_SOURCE_PATHS')) {
        define(
            'APP_SOURCE_PATHS',
            [
                'endpoints' => [
                    APP_PROTECTED_DIR_PATH . '/endpoints',
                    APP_FRAMEWORK_DIR_PATH . '/endpoints'
                ],

                'middlewares' => [
                    APP_PROTECTED_DIR_PATH . '/middlewares',
                    APP_FRAMEWORK_DIR_PATH . '/middlewares'
                ],

                'controllers' => [
                    APP_PROTECTED_DIR_PATH . '/controllers',
                    APP_FRAMEWORK_DIR_PATH . '/controllers'
                ],

                'models' => [
                    APP_PROTECTED_DIR_PATH . '/models',
                    APP_FRAMEWORK_DIR_PATH . '/models'
                ],

                'plugins' => [
                    APP_PROTECTED_DIR_PATH . '/plugins',
                    APP_FRAMEWORK_DIR_PATH . '/plugins'
                ],

                'hooks' => [
                    APP_PROTECTED_DIR_PATH . '/hooks'
                ],

                'config' => [
                    APP_PROTECTED_DIR_PATH . '/config'
                ],

                'lang' => [
                    APP_PROTECTED_DIR_PATH . '/lang'
                ],

                'themes' => [
                    APP_PROTECTED_DIR_PATH . '/themes'
                ],

                'pages' => [
                    APP_PROTECTED_DIR_PATH . '/pages'
                ]
            ]
        );
    }

    if (
        empty(APP_FRAMEWORK_DIR_PATH) ||
        empty(APP_PROTECTED_DIR_PATH) ||
        empty(APP_PUBLIC_DIR_PATH)
    ) {
        throw new Exception('Invalid Project Structure');
    }

    if (!defined('APP_MODE')) {
        define('APP_MODE', 'prod');
    }

    $coreEnvFilePath = sprintf(
        '%s/env/%s.php',
        APP_FRAMEWORK_DIR_PATH,
        APP_MODE
    );

    if (file_exists($coreEnvFilePath) && is_file($coreEnvFilePath)) {
        require_once $coreEnvFilePath;
    }

    if (defined('APP_API_MODE')) {
        require_once sprintf(
            '%s/env/%s.php',
            APP_FRAMEWORK_DIR_PATH,
            APP_API_MODE
        );
    }

    if (!defined('APP_AREA')) {
        define('APP_AREA', 'default');
    }

    if (!defined('APP_CACHE')) {
        define('APP_CACHE', true);
    }

    if (!defined('APP_CACHE_TTL')) {
        define('APP_CACHE_TTL', 5 * 60);
    }

    if (!defined('APP_ROUTING_TYPE')) {
        define('APP_ROUTING_TYPE', 'default');
    }

    if (!defined('APP_MULTI_LANGUAGE')) {
        define('APP_MULTI_LANGUAGE', false);
    }

    if (!defined('APP_RESPONSE_FORMAT')) {
        define('APP_RESPONSE_FORMAT', 'html');
    }

    require_once APP_FRAMEWORK_DIR_PATH . '/autoload.php';
    require_once APP_FRAMEWORK_DIR_PATH . '/app.php';

    (new Sonder\App)->run();
} catch (Throwable $thr) {
    $errorMessage = $thr->getMessage();

    $errorMessage = sprintf(
        'Can Not Start Application: %s',
        $errorMessage
    );

    echo $errorMessage;
    exit(0);
}
