<?php
namespace Core\Plugins\Database\Exceptions;

class DatabaseCacheException extends DatabaseException
{
    const MESSAGE_CACHE_ADAPER_NOT_ALLOWED = 'Database Cache Adapter Is Not '.
                                             'Allowed';

    const MESSAGE_CACHE_SQL_IS_EMPTY = 'Database Cache SQL Is Empty';
}
