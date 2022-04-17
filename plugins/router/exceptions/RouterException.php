<?php

namespace Sonder\Plugins\Router\Exceptions;

use Exception;

class RouterException extends Exception
{
    const CODE_PLUGIN_URL_IS_NOT_SET = 1001;

    const CODE_ENTITY_ROUTE_IS_NOT_SET = 2001;
    const CODE_ENTITY_CONTROLLER_IS_NOT_SET = 2002;
    const CODE_ENTITY_FILE_PATH_IS_NOT_SET = 2003;
    const CODE_ENTITY_METHOD_IS_NOT_SET = 2004;

    const CODE_CACHE_URL_IS_NOT_SET = 3001;
    const CODE_CACHE_ROUTE_IS_NOT_SET = 3002;
}
