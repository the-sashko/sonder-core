<?php

namespace Sonder\Exceptions;

final class CoreException extends AppException
{
    const MESSAGE_CORE_MODEL_NOT_EXISTS = 'Model "%s" Not Exists';

    const MESSAGE_CORE_PLUGIN_NOT_EXISTS = 'Plugin "%s" Not Exists';
}
