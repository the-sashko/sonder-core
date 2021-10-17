<?php

namespace Sonder\Plugins\Share\Exceptions;

use Exception;

class ShareException extends Exception
{
    const CODE_PLATFORM_IS_NOT_SET = 1001;
    const CODE_CREDENTIALS_IS_NOT_SET = 1002;
    const CODE_MESSAGE_IS_NOT_SET = 1003;
    const CODE_PLATFORM_IS_NOT_EXISTS = 1004;

    const CODE_MESSAGE_HAS_BAD_FORMAT = 2001;
    const CODE_INVALID_CREDENTIALS = 2002;
    const CODE_REMOTE_ERROR = 2003;
}
