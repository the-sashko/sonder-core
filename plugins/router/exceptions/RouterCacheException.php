<?php

namespace Sonder\Plugins\Router\Exceptions;

final class RouterCacheException extends RouterException
{
    const MESSAGE_CACHE_URL_IS_NOT_SET = 'Router Plugin Cache URL Is Not Set';

    const MESSAGE_CACHE_ROUTE_IS_NOT_SET = 'Router Plugin Cache Route Entity ' .
    'Is Not Set';
}
