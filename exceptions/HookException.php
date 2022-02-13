<?php

namespace Sonder\Exceptions;

final class HookException extends AppException
{
    const MESSAGE_HOOK_VALUE_IS_NOT_EXIST = 'Value "%s" Is Not Exist In ' .
    'Hook Values';
}
