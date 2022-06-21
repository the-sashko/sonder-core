<?php

namespace Sonder\Exceptions;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;

#[AppException]
#[ICoreException]
#[Attribute(Attribute::TARGET_CLASS)]
final class EndpointException extends AppException
{
    final public const MESSAGE_ENDPOINT_RESPONSE_IS_EMPTY = 'Response Is Empty';
    final public const MESSAGE_ENDPOINT_CONTROLLER_IS_NOT_SET = 'Controller Is Not Set';
    final public const MESSAGE_ENDPOINT_INVALID_CONTROLLER_METHOD = 'Invalid Controller Method';
}
