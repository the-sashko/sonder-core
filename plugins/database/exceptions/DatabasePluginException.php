<?php

namespace Sonder\Plugins\Database\Exceptions;

final class DatabasePluginException extends DatabaseException
{
    final public const MESSAGE_PLUGIN_UNKNOWN_ERROR = 'Database Unknown Error';
    final public const MESSAGE_PLUGIN_CONFIG_DATA_IS_EMPTY = 'Database Config Data Is Empty';
    final public const MESSAGE_PLUGIN_DSN_IS_EMPTY = 'Database DSN Is Empty';
    final public const MESSAGE_PLUGIN_SQL_IS_EMPTY = 'Database SQL Is Empty';
    final public const MESSAGE_PLUGIN_SQL_ERROR = 'Database SQL Query Error';
    final public const MESSAGE_PLUGIN_NOT_CONNECTED = 'Not Connected To Database';
    final public const MESSAGE_PLUGIN_CAN_NOT_CONNECT = 'Can Not Connect To Database';
}
