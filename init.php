<?php
try {
    define('APP_CORE_DIR_PATH', realpath(__DIR__));

    if (!defined('APP_PROTECTED_DIR_PATH')) {
        define(
            'APP_PROTECTED_DIR_PATH',
            realpath(APP_CORE_DIR_PATH . '/../')
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
                    APP_CORE_DIR_PATH . '/endpoints'
                ],

                'middlewares' => [
                    APP_PROTECTED_DIR_PATH . '/middlewares',
                    APP_CORE_DIR_PATH . '/middlewares'
                ],

                'controllers' => [
                    APP_PROTECTED_DIR_PATH . '/controllers',
                    APP_CORE_DIR_PATH . '/controllers'
                ],

                'models' => [
                    APP_PROTECTED_DIR_PATH . '/models',
                    APP_CORE_DIR_PATH . '/models'
                ],

                'hooks' => [
                    APP_PROTECTED_DIR_PATH . '/hooks',
                    APP_CORE_DIR_PATH . '/hooks'
                ],

                'plugins' => [
                    APP_PROTECTED_DIR_PATH . '/plugins',
                    APP_CORE_DIR_PATH . '/plugins'
                ],

                'lang' => [
                    APP_PROTECTED_DIR_PATH . '/lang'
                ],

                'pages' => [
                    APP_PROTECTED_DIR_PATH . '/pages'
                ]
            ]
        );
    }

    if (
        empty(APP_CORE_DIR_PATH) ||
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
        APP_CORE_DIR_PATH,
        APP_MODE
    );

    if (file_exists($coreEnvFilePath) && is_file($coreEnvFilePath)) {
        require_once $coreEnvFilePath;
    }

    if (!defined('APP_AREA')) {
        define('APP_AREA', 'default');
    }

    if (!defined('APP_MULTI_LANGUAGE')) {
        define('APP_MULTI_LANGUAGE', false);
    }

    if (!defined('APP_RESPONSE_TYPE')) {
        define('APP_RESPONSE_TYPE', 'html');
    }

    require_once APP_CORE_DIR_PATH . '/autoload.php';
    require_once APP_CORE_DIR_PATH . '/app.php';

    (new SonderCore\App)->run();
} catch (Exception $exp) {
    $errorMessage = $exp->getMessage();

    $errorMessage = sprintf(
        'Can Not Start Application: %s',
        $errorMessage
    );

    echo $errorMessage;
    exit(0);
}
