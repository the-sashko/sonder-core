<?php
namespace Sonder;

use Exception;

try {
    define('APP_CORE_DIR_PATH', realpath(__DIR__));
    define('APP_PROTECTED_DIR_PATH', realpath(APP_CORE_DIR_PATH . '/../'));
    define('APP_PUBLIC_DIR_PATH', realpath(APP_PROTECTED_DIR_PATH . '/../public'));

    if (
        empty(APP_CORE_DIR_PATH) ||
        empty(APP_PROTECTED_DIR_PATH) ||
        empty(APP_PUBLIC_DIR_PATH)
    ) {
        throw new Exception('Invalid Project Structure');
    }

    define('APP_CLI_DIR_PATH', realpath(APP_PROTECTED_DIR_PATH . '/../cli'));

    define('APP_ENV_DIR_PATH', __DIR__ . '/../env');
    define('CORE_ENV_DIR_PATH', __DIR__ . '/env');

    if (!defined('APP_ENV')) {
        define('APP_ENV', 'app');
    }

    $appEnvFilePath = sprintf(
        '%s/%s.php',
        APP_ENV_DIR_PATH,
        APP_ENV
    );

    $coreEnvFilePath = sprintf(
        '%s/%s.php',
        CORE_ENV_DIR_PATH,
        APP_ENV
    );

    if (file_exists($appEnvFilePath) && is_file($appEnvFilePath)) {
        require_once $appEnvFilePath;
    }

    if (file_exists($coreEnvFilePath) && is_file($coreEnvFilePath)) {
        require_once $coreEnvFilePath;
    }

    if (!defined('APP_MODE')) {
        define('APP_MODE', 'prod');
    }

    $coreEnvFilePath = sprintf(
        '%s/%s.php',
        CORE_ENV_DIR_PATH,
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

    require_once APP_CORE_DIR_PATH . '/app.php';

    (new App)->run();
} catch (Exception $exp) {
    $errorMessage = $exp->getMessage();

    $errorMessage = sprintf(
        'Can Not Start Application: %s',
        $errorMessage
    );

    echo $errorMessage;
    exit(0);
}
