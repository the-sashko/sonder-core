<?php

namespace Sonder\Plugins\Database\Exceptions;

final class DatabasePluginException extends DatabaseException
{
    const MESSAGE_PLUGIN_UNKNOWN_ERROR = 'Database Unknown Error';

    const MESSAGE_PLUGIN_CONFIG_DATA_IS_EMPTY = 'Database Config Data ' .
    'Is Empty';

    const MESSAGE_PLUGIN_DSN_IS_EMPTY = 'Database DSN Is Empty';

    const MESSAGE_PLUGIN_SQL_IS_EMPTY = 'Database SQL Is Empty';

    const MESSAGE_PLUGIN_SQL_ERROR = 'Database SQL Query Error';

    const MESSAGE_PLUGIN_NOT_CONNECTED = 'Not Connected To Database';

    const MESSAGE_PLUGIN_CAN_NOT_CONNECT = 'Can Not Connect To Database';
}
