<?php
namespace Core\Plugins\Session\Exceptions;

class SessionException extends \Exception
{
    const CODE_VALUE_NAME_IS_NOT_SET = 1001;
    const CODE_VALUE_IS_NOT_EXISTS   = 1002;

    const MESSAGE_VALUE_NAME_IS_NOT_SET = 'Session Plugin Value Name Is Name';
    const MESSAGE_VALUE_IS_NOT_EXISTS   = 'Session Plugin Value Is Not Exists';
}
