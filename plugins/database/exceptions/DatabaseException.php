<?php

namespace Sonder\Plugins\Database\Exceptions;

use Exception;
use Throwable;

class DatabaseException extends Exception implements Throwable
{
    final public const CODE_PLUGIN_UNKNOWN_ERROR = 1001;
    final public const CODE_PLUGIN_CONFIG_DATA_IS_EMPTY = 1002;
    final public const CODE_PLUGIN_DSN_IS_EMPTY = 1003;
    final public const CODE_PLUGIN_SQL_IS_EMPTY = 1004;
    final public const CODE_PLUGIN_SQL_ERROR = 1005;
    final public const CODE_PLUGIN_NOT_CONNECTED = 1006;
    final public const CODE_PLUGIN_CAN_NOT_CONNECT = 1007;

    final public const CODE_CREDENTIALS_CONFIG_DATA_IS_EMPTY = 2001;
    final public const CODE_CREDENTIALS_DB_TYPE_IS_NOT_SET = 2002;
    final public const CODE_CREDENTIALS_DB_NAME_IS_NOT_SET = 2003;
    final public const CODE_CREDENTIALS_DSN_IS_EMPTY = 2004;

    final public const CODE_CACHE_ADAPTER_NOT_ALLOWED = 3001;
    final public const CODE_CACHE_SQL_IS_EMPTY = 3002;
}
