<?php

namespace Sonder\Plugins\Database\Exceptions;

final class DatabaseCacheException extends DatabaseException
{
    const MESSAGE_CACHE_ADAPTER_NOT_ALLOWED = 'Database Cache Adapter Is Not ' .
    'Allowed';

    const MESSAGE_CACHE_SQL_IS_EMPTY = 'Database Cache SQL Is Empty';
}
