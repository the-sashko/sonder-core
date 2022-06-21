<?php

namespace Sonder\Exceptions;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;

#[AppException]
#[ICoreException]
#[Attribute(Attribute::TARGET_CLASS)]
final class RequestObjectException extends AppException
{
    final public const MESSAGE_REQUEST_HOST_NOT_SET = 'Host Is Not Set';
    final public const MESSAGE_REQUEST_UNSUPPORTED_HTTP_METHOD = 'Unsupported HTTP Method';
}
