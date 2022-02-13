<?php

namespace Sonder\Exceptions;

final class ApiException extends AppException
{
    const MESSAGE_API_URL_HAS_BAD_FORMAT = 'URL Has Bad Format';

    const MESSAGE_API_CAN_NOT_RETRIEVE_MODEL = 'Can Not Retrieve Model "%s"';

    const MESSAGE_API_NOT_SUPPORTED_API_CALLS = 'API Calls Are Not Supported ' .
    'In Model "%s"';

    const MESSAGE_API_INVALID_CRUD_ACTION = 'Invalid CRUD Action';

    const MESSAGE_API_INVALID_HTTP_METHOD = 'Invalid HTTP Method';
}
