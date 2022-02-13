<?php

namespace Sonder\Exceptions;

final class EndpointException extends AppException
{
    const MESSAGE_ENDPOINT_RESPONSE_IS_EMPTY = 'Response Is Empty';

    const MESSAGE_ENDPOINT_CONTROLLER_IS_NOT_SET = 'Controller Is Not Set';

    const MESSAGE_ENDPOINT_INVALID_CONTROLLER_METHOD = 'Invalid Controller ' .
    'Method';
}
