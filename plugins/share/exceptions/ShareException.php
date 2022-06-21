<?php

namespace Sonder\Plugins\Share\Exceptions;

use Exception;
use Throwable;

class ShareException extends Exception implements Throwable
{
    final public const CODE_PLATFORM_IS_NOT_SET = 1001;
    final public const CODE_CREDENTIALS_IS_NOT_SET = 1002;
    final public const CODE_MESSAGE_IS_NOT_SET = 1003;
    final public const CODE_PLATFORM_NOT_EXISTS = 1004;

    final public const CODE_MESSAGE_HAS_BAD_FORMAT = 2001;
    final public const CODE_INVALID_CREDENTIALS = 2002;
    final public const CODE_REMOTE_ERROR = 2003;
}
