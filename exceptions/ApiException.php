<?php

namespace Sonder\Exceptions;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;

#[AppException]
#[ICoreException]
#[Attribute(Attribute::TARGET_CLASS)]
final class ApiException extends AppException
{
    final public const MESSAGE_API_URL_HAS_BAD_FORMAT = 'URL Has Bad Format';
    final public const MESSAGE_API_CAN_NOT_RETRIEVE_MODEL = 'Can Not Retrieve Model "%s"';
    final public const MESSAGE_API_NOT_SUPPORTED_API_CALLS = 'API Calls Are Not Supported In Model "%s"';
    final public const MESSAGE_API_INVALID_CRUD_ACTION = 'Invalid CRUD Action';
    final public const MESSAGE_API_INVALID_HTTP_METHOD = 'Invalid HTTP Method';
    final public const MESSAGE_API_METHOD_IS_NOT_SUPPORTED = 'This Method Is Not Supported';
}
