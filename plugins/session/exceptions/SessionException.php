<?php

namespace Sonder\Plugins\Session\Exceptions;

use Exception;

final class SessionException extends Exception
{
    const CODE_VALUE_NAME_IS_NOT_SET = 1001;
    const CODE_FLASH_VALUE_NAME_IS_NOT_SET = 1002;
    const CODE_VALUE_NOT_EXISTS = 1003;

    const MESSAGE_VALUE_NAME_IS_NOT_SET = 'Session Plugin Value Name Is Name';

    const MESSAGE_FLASH_VALUE_NAME_IS_NOT_SET = 'Session Plugin Flash Value ' .
    'Name Is Name';

    const MESSAGE_FLASH_VALUE_NOT_EXISTS = 'Session Plugin Flash Value Not ' .
    'Exists';
}
