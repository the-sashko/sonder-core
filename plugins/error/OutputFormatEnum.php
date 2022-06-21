<?php

namespace Sonder\Plugins\Error;

enum OutputFormatEnum: string
{
    case HTML = 'html';
    case JSON = 'json';
    case TEXT = 'text';

    final public const DEFAULT = OutputFormatEnum::TEXT;
}
