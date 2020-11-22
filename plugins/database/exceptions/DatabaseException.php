<?php
namespace Core\Plugins\Database\Exceptions;

class DatabaseException extends \Exception
{
    const CODE_PLUGIN_UNKNOWN_ERROR        = 1001;
    const CODE_PLUGIN_CONFIG_DATA_IS_EMPTY = 1002;
    const CODE_PLUGIN_DSN_IS_EMPTY         = 1003;
    const CODE_PLUGIN_SQL_IS_EMPTY         = 1004;
    const CODE_PLUGIN_SQL_ERROR            = 1005;
    const CODE_PLUGIN_NOT_CONNECTED        = 1006;
    const CODE_PLUGIN_CAN_NOT_CONNECT      = 1007;

    const CODE_CREDENTIALS_CONFIG_DATA_IS_EMPTY = 2001;
    const CODE_CREDENTIALS_DB_TYPE_IS_NOT_SET   = 2002;
    const CODE_CREDENTIALS_DB_NAME_IS_NOT_SET   = 2003;
    const CODE_CREDENTIALS_DSN_IS_EMPTY         = 2004;

    const CODE_CACHE_ADAPER_NOT_ALLOWED = 3001;
    const CODE_CACHE_SQL_IS_EMPTY       = 3002;
}
