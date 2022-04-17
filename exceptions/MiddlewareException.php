<?php

namespace Sonder\Exceptions;

final class MiddlewareException extends AppException
{
    const MESSAGE_MIDDLEWARE_CONTROLLER_IS_NOT_SET = 'Controller Is set';

    const MESSAGE_MIDDLEWARE_METHOD_IS_NOT_SET = 'Method Is set';

    const MESSAGE_MIDDLEWARE_CSRF_RUNNING_BEFORE_SESSION = 'CSRF Middleware ' .
    'Must Be Run After Session Middleware';

    const MESSAGE_MIDDLEWARE_USER_RUNNING_BEFORE_SESSION = 'User Middleware ' .
    'Must Be Run After Session Middleware';

    const MESSAGE_MIDDLEWARE_ROUTING_TYPE_IS_NOT_SUPPORTED = 'Routing Type ' .
    '"%s" Is Not Supported';
}
