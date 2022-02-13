<?php

namespace Sonder\Exceptions;

final class HookException extends AppException
{
    const MESSAGE_HOOK_VALUE_NOT_EXISTS = 'Value "%s" Not Exists In Hook ' .
    'Values';
}
