<?php

namespace Sonder\Plugins\Session\Exceptions;

use Exception;
use Throwable;

final class SessionException extends Exception implements Throwable
{
    final public const MESSAGE_VALUE_NAME_IS_NOT_SET = 'Session Plugin Value "%s" Is Not Set';
    final public const MESSAGE_FLASH_VALUE_NAME_IS_NOT_SET = 'Session Plugin Flash Value "%s" Is Not Set';
    final public const MESSAGE_VALUE_NAME_IS_EMPTY = 'Session Plugin Value Name Is Empty';
    final public const MESSAGE_FLASH_VALUE_NAME_IS_EMPTY = 'Session Plugin Flash Value Name Is Empty';

    final public const CODE_VALUE_NAME_IS_NOT_SET = 1001;
    final public const CODE_FLASH_VALUE_NAME_IS_NOT_SET = 1002;
    final public const CODE_VALUE_NAME_IS_EMPTY = 1003;
    final public const CODE_FLASH_VALUE_NAME_IS_EMPTY = 1004;
}
