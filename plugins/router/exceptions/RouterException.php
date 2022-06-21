<?php

namespace Sonder\Plugins\Router\Exceptions;

use Exception;
use Throwable;

class RouterException extends Exception implements Throwable
{
    const MESSAGE_URL_IS_NOT_SET = 'Router Plugin URL Is Not Set';

    const CODE_URL_IS_NOT_SET = 1001;
}
