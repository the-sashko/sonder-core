<?php

namespace Sonder\Plugins\Router;

use Exception;
use Throwable;

final class RouterException extends Exception implements Throwable
{
    final public const MESSAGE_URL_IS_NOT_SET = 'Router Plugin URL Is Not Set';

    final public const CODE_URL_IS_NOT_SET = 1001;
}
