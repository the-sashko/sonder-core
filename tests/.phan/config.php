<?php

return [
    'target_php_version' => '7.2',

    'directory_list' => [
        '../'
    ],

    'exclude_file_list' => [
        '../install/protected/routers/default.php',
        '../plugins/qr/vendor/bindings/tcpdf/qrcode.php',
        '../plugins/qr/vendor/lib/full/qrencode.php',
        '../plugins/qr/vendor/lib/merged/phpqrcode.php'
    ],

    'exclude_analysis_directory_list' => [
        '../tests',
        '../install/protected/tests/',
        '../plugins/captcha/tests/',
        '../plugins/captcha/res/examples/',
        '../plugins/language/vendor',
        '../plugins/qr/vendor',
        '../plugins/redis/vendor',
        '../plugins/mail/providers/smtp/vendor',
        '../plugins/share/twitter/vendor'
    ]
];
