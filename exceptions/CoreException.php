<?php

namespace Sonder\Exceptions;

final class CoreException extends AppException
{
    const MESSAGE_CORE_MODEL_IS_NOT_EXIST = 'Model "%s" Is Not Exist';

    const MESSAGE_CORE_PLUGIN_IS_NOT_EXIST = 'Plugin "%s" Is Not Exist';
}
