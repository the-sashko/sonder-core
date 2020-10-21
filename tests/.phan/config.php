<?php

return [
    'target_php_version' => '7.2',

    'directory_list' => [
        '../'
    ],

    'exclude_analysis_directory_list' => [
        '../tests',
        '../plugins/captcha/tests/',
        '../plugins/captcha/res/examples/',
        '../plugins/language/vendor',
        '../plugins/qr/vendor',
        '../plugins/redis/vendor',
        '../plugins/mail/providers/SMTP/vendor',
        '../plugins/share/twitter/vendor'
    ],
];
